<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Scholarship;
use App\Models\Notification;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    public function apply(Scholarship $scholarship): RedirectResponse
    {
        $user = Auth::user();

        // Find the existing match record
        $application = Application::where('user_id', $user->id)
            ->where('scholarship_id', $scholarship->id)
            ->first();

        if (!$application) {
            return back()->with('error', 'You are not matched with this scholarship.');
        }

        if ($application->status !== 'matched') {
            return back()->with('error', 'You have already applied for this scholarship.');
        }

        $application->update([
            'status' => 'applied',
            'applied_at' => now(),
        ]);

        AuditLog::record('application.submitted', $application, null, [
            'scholarship' => $scholarship->name,
        ]);

        return back()->with('success', "Application submitted for {$scholarship->name}. Please upload your required documents.");
    }

    public function withdraw(Application $application): RedirectResponse
    {
        abort_if($application->user_id !== Auth::id(), 403);

        if (!in_array($application->status, ['matched', 'applied', 'documents_pending'])) {
            return back()->with('error', 'This application can no longer be withdrawn.');
        }

        $application->update(['status' => 'withdrawn']);

        AuditLog::record('application.withdrawn', $application);

        return back()->with('success', 'Application withdrawn.');
    }

    public function myApplications(): View
    {
        $user = Auth::user();

        $applications = Application::with('scholarship.category', 'documents')
            ->where('user_id', $user->id)
            ->orderByDesc('final_score')
            ->get();

        $unreadCount = Notification::where(function ($q) use ($user) {
            $q->where('recipient_id', $user->id)->orWhere('is_mass', 1);
        })
            ->whereDoesntHave('reads', fn($q) => $q->where('user_id', $user->id))
            ->count();

        return view('student.applications.index', compact('applications', 'user', 'unreadCount'));
    }
}
