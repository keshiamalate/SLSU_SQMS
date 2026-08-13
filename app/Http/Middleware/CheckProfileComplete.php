<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckProfileComplete
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->isStudent()) {
            return $next($request);
        }

        if ($request->routeIs('student.questionnaire.*')) {
            return $next($request);
        }

        $profile = $user->studentProfile;

        if (!$profile || !$profile->isComplete()) {
            return redirect()->route('student.questionnaire.show', ['step' => 1])
                ->with('info', 'Please complete your profile questionnaire first.');
        }

        return $next($request);
    }
}
