<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Company;
use Symfony\Component\HttpFoundation\Response;

class CheckLicenseExpired
{
    /**
     * Routes that should be excluded from license check
     */
    protected array $excludedRoutes = [
        'login',
        'logout',
        'password.request',
        'password.email',
        'password.reset',
        'license.expired',
        'sanctum.csrf-cookie',
    ];

    /**
     * Routes that start with these prefixes should be excluded
     */
    protected array $excludedPrefixes = [
        'api/',
        'admin/license/',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip license check for excluded routes
        if ($this->shouldSkipLicenseCheck($request)) {
            return $next($request);
        }

        // Get company (assuming single company system)
        $company = Company::first();

        // If no company exists, allow access (for initial setup)
        if (!$company) {
            return $next($request);
        }

        // Check if license is expired
        if ($company->isLicenseExpired()) {
            // If this is an AJAX/Livewire request, return JSON response
            if ($request->ajax() || $request->header('X-Livewire')) {
                return response()->json([
                    'message' => 'Your license has expired. Please contact support to renew your license.',
                    'license_expired' => true,
                    'redirect' => route('license.expired')
                ], 403);
            }

            // For regular requests, redirect to expired page
            return redirect()->route('license.expired')
                ->with('error', 'Your license has expired. Please contact support to renew your license.');
        }

        return $next($request);
    }

    /**
     * Check if the current request should skip license validation
     */
    protected function shouldSkipLicenseCheck(Request $request): bool
    {
        $currentRoute = Route::currentRouteName();

        // Check if route name is in excluded list
        if ($currentRoute && in_array($currentRoute, $this->excludedRoutes)) {
            return true;
        }

        // Check if URI starts with excluded prefixes
        $uri = $request->getRequestUri();
        foreach ($this->excludedPrefixes as $prefix) {
            if (str_starts_with($uri, '/' . $prefix)) {
                return true;
            }
        }

        return false;
    }
}
