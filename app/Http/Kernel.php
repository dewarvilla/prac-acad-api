<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * Global HTTP middleware stack.
     * Se ejecutan en cada request.
     */
    protected $middleware = [
        // Confianza en proxies (X-Forwarded-*)
        \App\Http\Middleware\TrustProxies::class,

        // CORS (si usas el paquete oficial de Laravel)
        \Illuminate\Http\Middleware\HandleCors::class,

        // Modo mantenimiento (503)
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,

        // Límite de tamaño de POST
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,

        // Trim de strings
        \App\Http\Middleware\TrimStrings::class,

        // Convierte strings vacíos a null
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * Route middleware groups.
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class, // si usas auth de sesión
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            // Si usas Sanctum para SPA:
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,

            // 👇 Mapeo global: camelCase -> snake_case (ENTRADA)
            \App\Http\Middleware\CamelToSnakeInput::class,

            // Rate limiting del API
            'throttle:api',

            // Bindings de rutas (model binding)
            \Illuminate\Routing\Middleware\SubstituteBindings::class,

            // 👇 (OPCIONAL) snake_case -> camelCase (SALIDA)
            // \App\Http\Middleware\SnakeToCamelResponse::class,
        ],
    ];

    /**
     * Route middleware aliases.
     * Puedes usarlos por nombre en rutas: ->middleware('auth'), 'can:edit', etc.
     */
    protected $middlewareAliases = [
        'auth'             => \App\Http\Middleware\Authenticate::class,
        'auth.basic'       => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session'     => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers'    => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can'              => \Illuminate\Auth\Middleware\Authorize::class,
        'guest'            => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed'           => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle'         => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified'         => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'bindings'         => \Illuminate\Routing\Middleware\SubstituteBindings::class,
    ];
}
