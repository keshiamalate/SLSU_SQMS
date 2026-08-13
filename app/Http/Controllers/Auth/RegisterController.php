<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function show()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'institutional_id' => [
                'required',
                'string',
                'max:30',
                'unique:users,institutional_id',
                'regex:/^[A-Za-z0-9\-]+$/',
            ],
            'email' => 'required|email|max:191|unique:users,email',
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'institutional_id.required' => 'Please enter your Student ID.',
            'institutional_id.unique' => 'This Student ID is already registered.',
            'institutional_id.regex' => 'Student ID may only contain letters, numbers, and hyphens.',
            'email.unique' => 'This email is already registered.',
            'password.confirmed' => 'Passwords do not match.',
            'password.min' => 'Password must be at least 8 characters.',
        ]);

        $studentRole = Role::where('name', 'student')->first();

        $user = User::create([
            'role_id' => $studentRole->id,
            'institutional_id' => strtoupper(trim($request->institutional_id)),
            'email' => $request->email,
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'password' => Hash::make($request->password),
            'is_active' => 1,
        ]);

        AuditLog::record('auth.registered', $user, null, [
            'institutional_id' => $user->institutional_id,
            'email' => $user->email,
        ]);

        // Auto login after registration
        Auth::login($user);

        return redirect()->route('consent.show')
            ->with('success', 'Account created successfully. Please review and accept the Data Privacy Consent.');
    }
}
