<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'institutional_id' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $credentials = [
            'institutional_id' => strtoupper(trim($request->institutional_id)),
            'password' => $request->password,
        ];

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('institutional_id'))
                ->withErrors(['institutional_id' => 'These credentials do not match our records.']);
        }

        $user = Auth::user();

        if (!$user->is_active) {
            Auth::logout();
            return back()->withErrors(['institutional_id' => 'Your account has been deactivated. Contact the scholarship office.']);
        }

        $user->update(['last_login_at' => now()]);

        AuditLog::record('auth.login', $user, null, null, "Logged in from {$request->ip()}");

        $request->session()->regenerate();

        if ($user->isStudent()) {
            return redirect()->intended(route('student.dashboard'));
        }

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request)
    {
        AuditLog::record('auth.logout', Auth::user());
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
