<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

// Custom middleware
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\IsAdmin;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',

        // Optional: Register additional route files
        then: function () {
            Route::middleware('web')
                ->prefix('admin')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        // ✅ Register SecurityHeaders middleware to the web group
        $middleware->web(append: [
            SecurityHeaders::class,
        ]);

        // ✅ Middleware aliases (can be used in routes)
        $middleware->alias([
            'admin' => IsAdmin::class,
            // Add more aliases here if needed
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // You can register custom exception handlers here if needed
    })
    ->create();
