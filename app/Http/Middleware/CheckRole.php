<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($role === 'admin') {
            $adminRoles = ['super_admin', 'scholarship_admin', 'verifier'];
            if (!in_array($user->role->name, $adminRoles)) {
                abort(403, 'Access denied.');
            }
            return $next($request);
        }

        if ($user->role->name !== $role) {
            if ($user->isStudent()) {
                return redirect()->route('student.dashboard')
                    ->with('error', 'You do not have access to that area.');
            }
            return redirect()->route('admin.dashboard')
                ->with('error', 'You do not have access to that area.');
        }

        return $next($request);
    }
}
