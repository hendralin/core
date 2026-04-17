<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StartSessionWithUserLifetime extends StartSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->applyUserSessionLifetime($request);

        return parent::handle($request, $next);
    }

    /**
     * Apply per-user session lifetime before the session starts.
     */
    protected function applyUserSessionLifetime(Request $request): void
    {
        $defaultLifetime = (int) config('session.lifetime', 120);

        if (config('session.driver') !== 'database') {
            config(['session.lifetime' => $defaultLifetime]);

            return;
        }

        $sessionTable = (string) config('session.table', 'sessions');
        $sessionCookieName = (string) config('session.cookie');
        $sessionId = $request->cookies->get($sessionCookieName);

        if (! filled($sessionId)) {
            config(['session.lifetime' => $defaultLifetime]);

            return;
        }

        try {
            if (! Schema::hasTable($sessionTable) || ! Schema::hasColumn('users', 'session_lifetime_minutes')) {
                config(['session.lifetime' => $defaultLifetime]);

                return;
            }

            $sessionRecord = DB::table($sessionTable)
                ->select('user_id')
                ->where('id', $sessionId)
                ->first();

            if (! $sessionRecord?->user_id) {
                config(['session.lifetime' => $defaultLifetime]);

                return;
            }

            $cachedLifetime = Cache::remember(
                'user.session_lifetime_minutes.'.$sessionRecord->user_id,
                now()->addMinutes(10),
                function () use ($sessionRecord) {
                    return (int) (User::query()
                        ->whereKey($sessionRecord->user_id)
                        ->value('session_lifetime_minutes') ?? 0);
                }
            );

            $lifetime = $cachedLifetime >= 5 ? $cachedLifetime : $defaultLifetime;

            config(['session.lifetime' => $lifetime]);
        } catch (\Throwable) {
            config(['session.lifetime' => $defaultLifetime]);
        }
    }
}
