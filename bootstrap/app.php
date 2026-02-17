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
    $middleware->alias([
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
        'hostel.manager' => \App\Http\Middleware\HostelManagerMiddleware::class,
        'student' => \App\Http\Middleware\StudentMiddleware::class,
    ]);

   // ✅ Make sure VerifyCsrfToken is in web middleware group
        $middleware->group('web', [
            \App\Http\Middleware\VerifyCsrfToken::class,  // MUST BE HERE
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);
})
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
