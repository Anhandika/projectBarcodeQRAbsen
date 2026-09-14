<?php

use App\Console\Commands\CleanupExpiredSessions;
use App\Http\Middleware\EnsureUserRole;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');
        $middleware->alias([
            'role' => EnsureUserRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Keep the default exception rendering for the prototype.
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('session:cleanup')->everyFiveMinutes();
    })
    ->create();
