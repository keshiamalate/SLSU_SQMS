<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationTemplate;
use App\Models\User;
use App\Models\Scholarship;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::with('sender', 'scholarship')
            ->latest('created_at')
            ->paginate(15);

        $templates = NotificationTemplate::all();

        return view('admin.notifications.index', compact('notifications', 'templates'));
    }

    public function create()
    {
        $templates = NotificationTemplate::all();
        $scholarships = Scholarship::where('is_active', 1)->get();

        $recipientGroups = [
            'all' => 'All Students',
            'profile_complete' => 'Students with Complete Profile',
            'profile_incomplete' => 'Students with Incomplete Profile',
            'matched' => 'Students with Matched Scholarships',
            'applied' => 'Students who Applied',
        ];

        return view('admin.notifications.create', compact(
            'templates',
            'scholarships',
            'recipientGroups'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'channel' => 'required|in:email,in_app,both',
            'recipient_group' => 'required|string',
            'scholarship_id' => 'nullable|exists:scholarships,id',
        ]);

        $recipients = $this->resolveRecipients($request->recipient_group);

        $notification = Notification::create([
            'sender_id' => Auth::id(),
            'scholarship_id' => $request->scholarship_id,
            'subject' => $request->subject,
            'body' => $request->body,
            'channel' => $request->channel,
            'is_mass' => 1,
            'sent_at' => now(),
        ]);

        // Send email if channel includes email
        if (in_array($request->channel, ['email', 'both'])) {
            foreach ($recipients as $user) {
                $this->sendEmail($user, $request->subject, $request->body);
            }
        }

        AuditLog::record(
            'notification.sent',
            $notification,
            null,
            [
                'recipients' => $recipients->count(),
                'channel' => $request->channel,
            ],
            "Mass notification sent to {$recipients->count()} students."
        );

        return redirect()->route('admin.notifications.index')
            ->with('success', "Notification sent to {$recipients->count()} students.");
    }

    public function sendDeadlineReminders()
    {
        $scholarships = Scholarship::where('is_active', 1)
            ->whereNotNull('application_close_at')
            ->where('application_close_at', '>=', now()->toDateString())
            ->where('application_close_at', '<=', now()->addDays(7)->toDateString())
            ->get();

        $sent = 0;

        foreach ($scholarships as $scholarship) {
            $students = User::whereHas('role', fn($q) => $q->where('name', 'student'))
                ->where('is_active', 1)
                ->get();

            $daysLeft = now()->diffInDays($scholarship->application_close_at);

            $notification = Notification::create([
                'sender_id' => Auth::id(),
                'scholarship_id' => $scholarship->id,
                'subject' => "Deadline Reminder: {$scholarship->name}",
                'body' => "This is a reminder that the application deadline for {$scholarship->name} is in {$daysLeft} day(s), on {$scholarship->application_close_at}. Please ensure you have submitted your application.",
                'channel' => 'both',
                'is_mass' => 1,
                'sent_at' => now(),
            ]);

            foreach ($students as $student) {
                $this->sendEmail(
                    $student,
                    "Deadline Reminder: {$scholarship->name}",
                    "This is a reminder that the application deadline for {$scholarship->name} is in {$daysLeft} day(s)."
                );
            }

            $sent++;
        }

        return back()->with('success', "Deadline reminders sent for {$sent} scholarship(s).");
    }

    private function resolveRecipients(string $group)
    {
        $base = User::whereHas('role', fn($q) => $q->where('name', 'student'))
            ->where('is_active', 1);

        return match ($group) {
            'all' => $base->get(),
            'profile_complete' => $base->whereHas('studentProfile', fn($q) => $q->whereNotNull('profile_completed_at'))->get(),
            'profile_incomplete' => $base->whereDoesntHave('studentProfile')->orWhereHas('studentProfile', fn($q) => $q->whereNull('profile_completed_at'))->get(),
            'matched' => $base->whereHas('applications', fn($q) => $q->where('status', 'matched'))->get(),
            'applied' => $base->whereHas('applications', fn($q) => $q->where('status', 'applied'))->get(),
            default => $base->get(),
        };
    }

    private function sendEmail(User $user, string $subject, string $body): void
    {
        Mail::html(
            view('emails.notification', compact('user', 'subject', 'body'))->render(),
            function ($message) use ($user, $subject) {
                $message->to($user->email, $user->full_name)
                    ->subject($subject);
            }
        );
    }
}
