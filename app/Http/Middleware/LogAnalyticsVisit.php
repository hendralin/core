<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogAnalyticsVisit
{
    /**
     * Log one visit per GET/HEAD request for analytics (after response).
     * Skips Livewire POST endpoints and paths listed in config('visitor.except').
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! in_array($request->method(), ['GET', 'HEAD'], true)) {
            return $response;
        }

        if ($request->routeIs('license.expired')) {
            return $response;
        }

        foreach (config('visitor.except', []) as $pattern) {
            if ($request->is($pattern)) {
                return $response;
            }
        }

        try {
            $visitable = $this->resolveVisitable($request);
            visitor()->visit($visitable);
        } catch (\Throwable $e) {
            report($e);
        }

        return $response;
    }

    private function resolveVisitable(Request $request): ?Model
    {
        $route = $request->route();
        if (! $route) {
            return null;
        }

        foreach ($route->parameters() as $parameter) {
            if ($parameter instanceof Model) {
                return $parameter;
            }
        }

        return null;
    }
}
