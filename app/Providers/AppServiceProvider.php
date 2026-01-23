<?php

namespace App\Providers;

use Dedoc\Scramble\Scramble;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        // Scramble::configure()
        //     ->withDocumentTransformers(function (OpenApi $document) {
        //         $document->info->description = 'API for the best Stock Signal app!';
        //     });

        Scramble::configure()
            ->withDocumentTransformers(function (OpenApi $openApi) {
                $openApi->secure(
                    SecurityScheme::http('Bearer')
                );
            });
    }

    /**
     * Configure the rate limiters for the application.
     */
    private function configureRateLimiting(): void
    {
        // Default API rate limiter - 60 requests per minute
        RateLimiter::for('api', fn(Request $request) => Limit::perMinute(60)->by($request->user()?->id ?: $request->ip())->response(function () {
            return response('Too Many Requests', 429);
        }));

        // Auth endpoints - more restrictive (prevent brute force)
        RateLimiter::for('auth', fn(Request $request) => Limit::perMinute(5)->by($request->ip())->response(function () {
            return response('Too Many Requests', 429);
        }));

        // Authenticated user requests - higher limit
        RateLimiter::for('authenticated', fn(Request $request) => $request->user()
            ? Limit::perMinute(120)->by($request->user()->id)
            : Limit::perMinute(60)->by($request->ip())->response(function () {
                return response('Too Many Requests', 429);
            }));
    }
}
