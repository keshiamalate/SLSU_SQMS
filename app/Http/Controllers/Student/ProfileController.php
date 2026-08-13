<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show() {
        $user    = Auth::user()->load('studentProfile', 'role', 'existingScholarships');
        $profile = $user->studentProfile;

        $unreadCount = Notification::where(function ($q) use ($user) {
                $q->where('recipient_id', $user->id)->orWhere('is_mass', 1);
            })
            ->whereDoesntHave('reads', fn($q) => $q->where('user_id', $user->id))
            ->count();

        return view('student.profile.index', compact('user', 'profile', 'unreadCount'));
    }

    public function updatePersonal(Request $request) {
        $user = Auth::user();

        $request->validate([
            'first_name'  => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name'   => 'required|string|max:100',
            'email'       => 'required|email|unique:users,email,' . $user->id,
        ]);

        $old = $user->only(['first_name', 'middle_name', 'last_name', 'email']);

        $user->update([
            'first_name'  => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name'   => $request->last_name,
            'email'       => $request->email,
        ]);

        AuditLog::record('profile.personal_updated', $user, $old, $user->fresh()->toArray());

        return back()->with('success', 'Personal information updated successfully.');
    }

    public function updatePassword(Request $request) {
        $request->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:8|confirmed',
        ], [
            'password.confirmed' => 'Passwords do not match.',
            'password.min'       => 'Password must be at least 8 characters.',
        ]);

        $user = Auth::user();

        if (! Hash::check($request->current_password, $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Current password is incorrect.'])
                ->with('active_tab', 'security');
        }

        $user->update(['password' => Hash::make($request->password)]);

        AuditLog::record('auth.password_changed', $user);

        return back()
            ->with('success', 'Password changed successfully.')
            ->with('active_tab', 'security');
    }
}
