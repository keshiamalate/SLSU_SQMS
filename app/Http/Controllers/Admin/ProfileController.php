<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show() {
        $admin = Auth::user()->load('role');

        $recentActivity = AuditLog::where('user_id', $admin->id)
            ->latest('created_at')
            ->take(10)
            ->get();

        return view('admin.profile.index', compact('admin', 'recentActivity'));
    }

    public function update(Request $request) {
        $admin = Auth::user();

        $request->validate([
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $admin->id,
        ]);

        $old = $admin->only(['first_name', 'middle_name', 'last_name', 'email']);

        $admin->update([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
        ]);

        AuditLog::record('profile.updated', $admin, $old, $admin->fresh()->toArray());

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request) {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.confirmed' => 'Passwords do not match.',
            'password.min' => 'Password must be at least 8 characters.',
        ]);

        $admin = Auth::user();

        if (!Hash::check($request->current_password, $admin->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $admin->update(['password' => Hash::make($request->password)]);

        AuditLog::record('auth.password_changed', $admin);

        return back()->with('success', 'Password changed successfully.');
    }
}
