<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\AgentMiddleware;
use App\Http\Middleware\CheckAgentApproved;
use App\Http\Middleware\HostelManagerMiddleware;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\StudentMiddleware;
use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Auth\Middleware\Authenticate;
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
            'auth' => Authenticate::class,
            'admin' => AdminMiddleware::class,
            'hostel.manager' => HostelManagerMiddleware::class,
            'student' => StudentMiddleware::class,
            'hostel.agent' => AgentMiddleware::class,
            'agent.approved' => CheckAgentApproved::class,
            'role' => RoleMiddleware::class,
        ]);

        // ✅ CORRECT: Append to existing web middleware group
        $middleware->web(append: [
            VerifyCsrfToken::class,
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
