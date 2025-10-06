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
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
                    'ok'      => false,
                    'code'    => 401,
                    'message' => 'No autenticado.',
                ], 401);
            }
        });

        // 422 – validación
        $this->renderable(function (ValidationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'ok'      => false,
                    'code'    => 422,
                    'message' => 'Los datos enviados son inválidos.',
                    'errors'  => $e->errors(),
                ], 422);
            }
        });

        // 403 – autorización
        $this->renderable(function (AuthorizationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'ok'      => false,
                    'code'    => 403,
                    'message' => 'No tienes permisos para esta acción.',
                ], 403);
            }
        });

        // 404 – modelo no encontrado
        $this->renderable(function (ModelNotFoundException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'ok'      => false,
                    'code'    => 404,
                    'message' => 'Recurso no encontrado.',
                ], 404);
            }
        });

        // 404 – ruta no encontrada (útil cuando no existe endpoint)
        $this->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'ok'      => false,
                    'code'    => 404,
                    'message' => 'Ruta o recurso no encontrado.',
                ], 404);
            }
        });

        // 405 – método HTTP no permitido
        $this->renderable(function (MethodNotAllowedHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'ok'      => false,
                    'code'    => 405,
                    'message' => 'Método HTTP no permitido para esta ruta.',
                ], 405);
            }
        });

        // 429 – rate limit
        $this->renderable(function (ThrottleRequestsException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'ok'      => false,
                    'code'    => 429,
                    'message' => 'Demasiadas solicitudes. Inténtalo más tarde.',
                ], 429);
            }
        });

        /**
         * 409 – conflictos de base de datos con mensajes específicos
         * - MySQL/MariaDB:
         *   - 1062 → UNIQUE
         *   - 1451 → FK al borrar/actualizar (padre con hijos)
         *   - 1452 → FK al insertar/actualizar (hijo sin padre)
         *   - 1216/1217 → otros FK
         * - PostgreSQL:
         *   - 23505 → UNIQUE
         *   - 23503 → FK
         */
        $this->renderable(function (QueryException $e, $request) {
            if (! $request->expectsJson()) {
                return;
            }

            $sqlState = $e->getCode(); // '23000' (MySQL) / '23505' (PG) / etc
            $driver   = \DB::connection()->getDriverName(); // 'mysql'|'pgsql'|'sqlsrv'
            $errno    = (int) ($e->errorInfo[1] ?? 0);      // código numérico MySQL

            // MySQL / MariaDB
            if ($driver === 'mysql' && $sqlState === '23000') {
                switch ($errno) {
                    case 1062: // Duplicate entry (UNIQUE)
                        $det = $this->parseMysqlDuplicateKey($e);
                        return response()->json([
                            'ok'         => false,
                            'code'       => 409,
                            'message'    => 'Ya existe un registro con esos datos (violación de restricción única).',
                            'constraint' => $det['key'] ?? null,
                            'entry'      => $det['entry'] ?? null,
                            'hint'       => app()->isLocal() ? $e->getMessage() : null,
                        ], 409);

                    case 1451: // Cannot delete or update parent row (FK en delete/update)
                        return response()->json([
                            'ok'      => false,
                            'code'    => 409,
                            'message' => 'No se puede eliminar/actualizar: existen registros relacionados (restricción de clave foránea).',
                            'hint'    => app()->isLocal() ? $e->getMessage() : null,
                        ], 409);

                    case 1452: // Cannot add or update child row (FK en insert/update)
                        return response()->json([
                            'ok'      => false,
                            'code'    => 409,
                            'message' => 'No se puede guardar: referencia a un recurso inexistente (clave foránea inválida).',
                            'hint'    => app()->isLocal() ? $e->getMessage() : null,
                        ], 409);

                    case 1216:
                    case 1217:
                        return response()->json([
                            'ok'      => false,
                            'code'    => 409,
                            'message' => 'Operación no permitida por restricciones de integridad referencial.',
                            'hint'    => app()->isLocal() ? $e->getMessage() : null,
                        ], 409);
                }

                // Genérico MySQL 23000
                return response()->json([
                    'ok'      => false,
                    'code'    => 409,
                    'message' => 'Conflicto con el estado actual del recurso (restricción de integridad).',
                    'hint'    => app()->isLocal() ? $e->getMessage() : null,
                ], 409);
            }

            // PostgreSQL
            if ($driver === 'pgsql') {
                if ($sqlState === '23505') { // unique_violation
                    return response()->json([
                        'ok'      => false,
                        'code'    => 409,
                        'message' => 'Ya existe un registro con esos datos (violación de restricción única).',
                        'hint'    => app()->isLocal() ? $e->getMessage() : null,
                    ], 409);
                }
                if ($sqlState === '23503') { // foreign_key_violation
                    return response()->json([
                        'ok'      => false,
                        'code'    => 409,
                        'message' => 'Operación no permitida por clave foránea (registro relacionado).',
                        'hint'    => app()->isLocal() ? $e->getMessage() : null,
                    ], 409);
                }
            }

            // Otros errores de consulta → 500
            return response()->json([
                'ok'      => false,
                'code'    => 500,
                'message' => 'Error de base de datos.',
                'error'   => app()->isLocal() ? $e->getMessage() : null,
            ], 500);
        });

        // 4xx/5xx lanzados con abort() o HttpException explícitas
        $this->renderable(function (HttpExceptionInterface $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'ok'      => false,
                    'code'    => $e->getStatusCode(),
                    'message' => $e->getMessage() ?: 'Error en la solicitud.',
                ], $e->getStatusCode(), $e->getHeaders());
            }
        });

        // 500 – cualquier otro no controlado (sólo reporta/loguea)
        $this->reportable(function (Throwable $e) {
            // Integra aquí Sentry/Bugsnag/Raygun si lo usas.
        });
    }

    /**
     * Extra: parsea mensaje MySQL 1062 para extraer clave e "entry" duplicada.
     */
    private function parseMysqlDuplicateKey(QueryException $e): array
    {
        $msg   = (string) $e->getMessage();
        $entry = null;
        $key   = null;

        // Ejemplos de formatos:
        // "SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry 'X-Y-1' for key 'creaciones_nombre_programa_active_unique'"
        // "Duplicate entry 'foo' for key 'PRIMARY'"
        if (preg_match("/Duplicate entry '(.+?)' for key '(.+?)'/", $msg, $m)) {
            $entry = $m[1] ?? null;
            $key   = $m[2] ?? null;
        } elseif (preg_match("/for key '(.+?)'/", $msg, $m)) {
            $key = $m[1] ?? null;
        }

        return compact('entry', 'key');
    }
}
