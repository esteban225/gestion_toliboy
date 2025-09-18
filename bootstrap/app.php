<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Providers\ModuleServiceProvider;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Middlewares globales
        $middleware->append([
            // \App\Http\Middleware\TrustProxies::class,
        ]);

        // Middlewares de API
        $middleware->api([
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        // Aliases
        $middleware->alias([
            'verified'    => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            'jwt.auth'    => \Tymon\JWTAuth\Http\Middleware\Authenticate::class,
            'jwt.refresh' => \Tymon\JWTAuth\Http\Middleware\RefreshToken::class,
            'role'        => \App\Http\Middleware\RoleAuthorization::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->withProviders([
        ModuleServiceProvider::class, // 👈 aquí va tu provider
    ])
    ->create();
