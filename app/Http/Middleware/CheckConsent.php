<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckConsent
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->isStudent()) {
            return $next($request);
        }

        if ($request->routeIs('consent.*')) {
            return $next($request);
        }

        if (!$user->hasValidConsent()) {
            return redirect()->route('consent.show')
                ->with('info', 'Please accept the Data Privacy Consent before continuing.');
        }

        return $next($request);
    }
}
