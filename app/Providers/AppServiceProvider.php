<?php

namespace App\Providers;

use App\Models\Company;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
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
        $defaultLifetime = (int) config('session.lifetime', 120);

        try {
            if (! Schema::hasTable('companies')) {
                return;
            }

            $sessionLifetime = Cache::rememberForever('app.session_lifetime_minutes', function () use ($defaultLifetime) {
                $company = Company::query()->first();

                return $company?->resolveSessionLifetimeMinutes($defaultLifetime) ?? $defaultLifetime;
            });

            config([
                'session.lifetime' => (int) $sessionLifetime,
            ]);
        } catch (\Throwable) {
            config([
                'session.lifetime' => $defaultLifetime,
            ]);
        }
    }
}
