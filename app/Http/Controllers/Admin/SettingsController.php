<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function index()
    {
        // Only super_admin can access settings
        abort_if(!Auth::user()->hasRole('super_admin'), 403);

        $settings = SystemSetting::all()->keyBy('key');

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        abort_if(!Auth::user()->hasRole('super_admin'), 403);

        $request->validate([
            'consent_version' => 'required|string|max:20',
            'session_timeout_minutes' => 'required|integer|min:5|max:480',
            'max_upload_size_mb' => 'required|integer|min:1|max:50',
            'allowed_upload_types' => 'required|string|max:100',
            'min_rf_accuracy' => 'required|numeric|between:0,1',
            'min_rf_f1' => 'required|numeric|between:0,1',
        ]);

        $keys = [
            'consent_version',
            'session_timeout_minutes',
            'max_upload_size_mb',
            'allowed_upload_types',
            'min_rf_accuracy',
            'min_rf_f1',
        ];

        foreach ($keys as $key) {
            if ($request->has($key)) {
                SystemSetting::setValue($key, $request->input($key), Auth::id());
            }
        }

        AuditLog::record('settings.updated', null, null, $request->only($keys));

        return back()->with('success', 'Settings saved successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        AuditLog::record('auth.password_changed', $user);

        return back()->with('success', 'Password changed successfully.');
    }
}
