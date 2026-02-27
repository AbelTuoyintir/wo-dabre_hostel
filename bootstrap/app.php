<?php

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
        // Register your middleware aliases
        $middleware->alias([
            'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'hostel.manager' => \App\Http\Middleware\HostelManagerMiddleware::class,
            'student' => \App\Http\Middleware\StudentMiddleware::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);

        // ✅ CORRECT: Append to existing web middleware group
        $middleware->web(append: [
            \App\Http\Middleware\VerifyCsrfToken::class,
        ]);

        // If you need to add middleware to API group
        // $middleware->api(append: [
        //     \App\Http\Middleware\YourApiMiddleware::class,
        // ]);

        // If you need global middleware
        // $middleware->append(\App\Http\Middleware\YourGlobalMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();