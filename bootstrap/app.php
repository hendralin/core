<?php

use App\Http\Middleware\CheckLicenseExpired;
use App\Http\Middleware\CheckUserStatus;
use App\Http\Middleware\LogAnalyticsVisit;
use App\Http\Middleware\StartSessionWithUserLifetime;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\Middleware\StartSession;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            'license.check' => CheckLicenseExpired::class,
        ]);

        $middleware->web(
            append: [
                CheckUserStatus::class,
                CheckLicenseExpired::class,
                LogAnalyticsVisit::class,
            ],
            replace: [
                StartSession::class => StartSessionWithUserLifetime::class,
            ],
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
