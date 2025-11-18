<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->isInactive()) {
            Auth::logout();

            // Don't redirect for API requests
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Your account is inactive. Please contact administrator.'], 403);
            }

            return redirect('/login')->withErrors([
                'email' => 'Your account is inactive. Please contact administrator.'
            ]);
        }

        return $next($request);
    }
}
