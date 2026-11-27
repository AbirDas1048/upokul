<?php

use App\Http\Middleware\DraftPreview;
use App\Http\Middleware\EnsureAdminActive;
use App\Http\Middleware\InstallationGuard;
use App\Http\Middleware\NoStore;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Must come before StartSession: until the wizard has run there is no sessions table.
        $middleware->web(prepend: [InstallationGuard::class]);
        $middleware->web(append: [SecurityHeaders::class, DraftPreview::class]);
        $middleware->alias([
            'admin.active' => EnsureAdminActive::class,
            'no-store' => NoStore::class,
        ]);
        $middleware->redirectGuestsTo(fn () => route('admin.login'));
        // Trusted proxies (TRUSTED_PROXIES) are applied in AppServiceProvider::boot(), after config loads.
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
