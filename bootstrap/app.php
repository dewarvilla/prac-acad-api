<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: base_path('routes/web.php'),
        api: base_path('routes/api.php'),
        commands: base_path('routes/console.php'),
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->appendToGroup('api', [
            \App\Http\Middleware\CamelToSnakeInput::class,
        ]);

        // aplicarlo a todas las rutas (web+api) en vez de solo 'api':
        // $middleware->append(\App\Http\Middleware\CamelToSnakeInput::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
