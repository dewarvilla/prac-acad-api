<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class Handler extends ExceptionHandler
{
    /**
     * Registra los mapeos de excepciones → respuestas JSON.
     */
    public function register(): void
    {
        // 401 – no autenticado
        $this->renderable(function (AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => false,
                    'code' => 401,
                    'message' => 'No autenticado.',
                ], 401);
            }
        });

        // 422 – validación
        $this->renderable(function (ValidationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'ok'    => false,
                    'code'  => 422,
                    'message' => 'Los datos enviados son inválidos.',
                    'errors'  => $e->errors(),
                ], 422);
            }
        });

        // 403 – autorización
        $this->renderable(function (AuthorizationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => false,
                    'code' => 403,
                    'message' => 'No tienes permisos para esta acción.',
                ], 403);
            }
        });

        // 404 – modelo no encontrado
        $this->renderable(function (ModelNotFoundException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => false,
                    'code' => 404,
                    'message' => 'Recurso no encontrado.',
                ], 404);
            }
        });

        // 429 – rate limit
        $this->renderable(function (ThrottleRequestsException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => false,
                    'code' => 429,
                    'message' => 'Demasiadas solicitudes. Inténtalo más tarde.',
                ], 429);
            }
        });

        // 409 – conflictos (p.ej. UNIQUE/FOREIGN KEY)
        $this->renderable(function (QueryException $e, $request) {
            if ($request->expectsJson() && $e->getCode() === '23000') {
                return response()->json([
                    'ok' => false,
                    'code' => 409,
                    'message' => 'Conflicto con el estado actual del recurso.',
                ], 409);
            }
        });

        // 4xx/5xx lanzados con abort() o HttpException
        $this->renderable(function (HttpExceptionInterface $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => false,
                    'code' => $e->getStatusCode(),
                    'message' => $e->getMessage() ?: 'Error en la solicitud.',
                ], $e->getStatusCode(), $e->getHeaders());
            }
        });

        // 500 – cualquier otro no controlado (solo reporta/loguea)
        $this->reportable(function (Throwable $e) {
            // Integra aquí Sentry/Bugsnag si lo usas.
        });
    }
}

