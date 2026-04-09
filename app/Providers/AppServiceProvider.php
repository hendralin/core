<?php

namespace App\Providers;

use App\Models\User;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

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

        Gate::define('viewApiDocs', function (User $user) {
            return in_array($user->email, ['hendra.lin@invyra.cloud']);
        });

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
