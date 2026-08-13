<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\AuditLog;
use App\Models\DocumentUpload;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $query = Application::with('user.studentProfile', 'scholarship.category')
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('scholarship')) {
            $query->where('scholarship_id', $request->scholarship);
        }

        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                    ->orWhere('last_name', 'like', '%' . $request->search . '%')
                    ->orWhere('institutional_id', 'like', '%' . $request->search . '%');
            });
        }

        $applications = $query->paginate(15)->withQueryString();

        $statusCounts = [
            'all' => Application::count(),
            'matched' => Application::where('status', 'matched')->count(),
            'applied' => Application::where('status', 'applied')->count(),
            'under_review' => Application::where('status', 'under_review')->count(),
            'documents_pending' => Application::where('status', 'documents_pending')->count(),
            'approved' => Application::where('status', 'approved')->count(),
            'rejected' => Application::where('status', 'rejected')->count(),
        ];

        return view('admin.applications.index', compact('applications', 'statusCounts'));
    }

    public function show(Application $application)
    {
        $application->load([
            'user.studentProfile',
            'scholarship.criteria',
            'scholarship.category',
            'documents',
            'reviewer',
        ]);

        return view('admin.applications.show', compact('application'));
    }

    public function updateStatus(Request $request, Application $application)
    {
        $request->validate([
            'status' => 'required|in:applied,under_review,documents_pending,approved,rejected',
            'decision_notes' => 'nullable|string|max:1000',
        ]);

        // Block withdrawn applications
        if ($application->status === 'withdrawn') {
            return back()->with('error', 'This application was withdrawn by the student and cannot be updated.');
        }

        $old = $application->only(['status', 'decision_notes']);
        $newStatus = $request->status;

        $application->update([
            'status' => $newStatus,
            'decision_notes' => $request->decision_notes,
            'reviewed_at' => now(),
            'reviewed_by' => Auth::id(),
        ]);

        $application->load('user', 'scholarship');
        $student = $application->user;
        $scholarship = $application->scholarship;

        // ── Notification messages per status ──────────────────────────
        $messages = [
            'under_review' => [
                'subject' => "Your Application is Under Review — {$scholarship->name}",
                'body' => "Dear {$student->first_name},\n\n"
                    . "Your application for the {$scholarship->name} scholarship is now under review by our scholarship committee.\n\n"
                    . "We will notify you once a decision has been made.\n\n"
                    . ($request->decision_notes
                        ? "Note from reviewer:\n{$request->decision_notes}\n\n"
                        : '')
                    . "Log in to SmartMatch to track your application status.",
            ],
            'documents_pending' => [
                'subject' => "Action Required: Submit Documents — {$scholarship->name}",
                'body' => "Dear {$student->first_name},\n\n"
                    . "Your application for the {$scholarship->name} scholarship requires document submission.\n\n"
                    . "Please log in to SmartMatch and upload all required documents as soon as possible.\n\n"
                    . ($request->decision_notes
                        ? "Note: {$request->decision_notes}\n\n"
                        : ''),
            ],
            'approved' => [
                'subject' => "🎉 Congratulations! Scholarship Approved — {$scholarship->name}",
                'body' => "Dear {$student->first_name},\n\n"
                    . "We are pleased to inform you that your application for the {$scholarship->name} scholarship has been APPROVED.\n\n"
                    . "Allowance: {$scholarship->formatted_allowance}\n\n"
                    . ($request->decision_notes
                        ? "Message from the scholarship office:\n{$request->decision_notes}\n\n"
                        : '')
                    . "Please log in to SmartMatch for further instructions regarding your scholarship.",
            ],
            'rejected' => [
                'subject' => "Application Update — {$scholarship->name}",
                'body' => "Dear {$student->first_name},\n\n"
                    . "After careful review, we regret to inform you that your application for the {$scholarship->name} scholarship was not approved at this time.\n\n"
                    . ($request->decision_notes
                        ? "Reason: {$request->decision_notes}\n\n"
                        : '')
                    . "You may apply for other scholarships you qualify for. Log in to SmartMatch to view your options.",
            ],
            'applied' => [
                'subject' => "Application Received — {$scholarship->name}",
                'body' => "Dear {$student->first_name},\n\n"
                    . "Your application for the {$scholarship->name} scholarship has been received and is being processed.\n\n"
                    . ($request->decision_notes
                        ? "Note: {$request->decision_notes}\n\n"
                        : '')
                    . "Log in to SmartMatch to track your application status.",
            ],
        ];

        if (isset($messages[$newStatus])) {
            $msg = $messages[$newStatus];

            // ── Save in-app notification ───────────────────────────────
            Notification::create([
                'sender_id' => Auth::id(),
                'recipient_id' => $student->id,
                'scholarship_id' => $scholarship->id,
                'subject' => $msg['subject'],
                'body' => $msg['body'],
                'channel' => 'both',
                'is_mass' => 0,
                'sent_at' => now(),
            ]);

            // ── Send email ─────────────────────────────────────────────
            try {
                Mail::html(
                    view('emails.notification', [
                        'user' => $student,
                        'subject' => $msg['subject'],
                        'body' => nl2br(e($msg['body'])),
                    ])->render(),
                    function ($message) use ($student, $msg) {
                        $message->to($student->email, $student->full_name)
                            ->subject($msg['subject']);
                    }
                );
            } catch (\Throwable $e) {
                Log::error("Status email failed for user {$student->id}: " . $e->getMessage());
                // Don't stop execution — in-app notification already saved
            }
        }

        AuditLog::record(
            'application.status_updated',
            $application,
            $old,
            [
                'status' => $newStatus,
                'notes' => $request->decision_notes,
            ]
        );

        $statusLabel = ucfirst(str_replace('_', ' ', $newStatus));

        return back()->with('success', "Status updated to {$statusLabel}. Student has been notified via in-app and email.");
    }

    public function verifyDocument(Request $request, DocumentUpload $document)
    {
        $request->validate([
            'verification_status' => 'required|in:verified,rejected',
            'rejection_reason' => 'required_if:verification_status,rejected|nullable|string',
        ]);

        $document->update([
            'verification_status' => $request->verification_status,
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        AuditLog::record('document.verified', $document, null, [
            'status' => $request->verification_status,
        ]);

        return back()->with('success', 'Document ' . $request->verification_status . '.');
    }
}
