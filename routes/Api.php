<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\Api\V1\ProgramacionController;
use App\Http\Controllers\Api\V1\SalarioController;
use App\Http\Controllers\Api\V1\ParticipanteController;
use App\Http\Controllers\Api\V1\AuxilioController;
use App\Http\Controllers\Api\V1\RutaController;
use App\Http\Controllers\Api\V1\ReprogramacionController;
use App\Http\Controllers\Api\V1\LegalizacionController;
use App\Http\Controllers\Api\V1\FechaController;
use App\Http\Controllers\Api\V1\CreacionController;
use App\Http\Controllers\Api\V1\AjusteController;
use App\Http\Controllers\Api\V1\CatalogoController;

Route::prefix('v1')
    ->middleware([
        'api',
        \App\Http\Middleware\CamelToSnakeInput::class, // ← usa la clase directamente
    ])
    ->group(function () {

        Route::post('catalogos/bulk',        [CatalogoController::class, 'storeBulk'])->name('catalogos.bulk');
        Route::post('catalogos/bulk-delete', [CatalogoController::class, 'destroyBulk'])->name('catalogos.bulk-delete');
        Route::apiResource('catalogos', CatalogoController::class)->parameters(['catalogos' => 'catalogo']);

        Route::apiResource('programaciones',   ProgramacionController::class)->parameters(['programaciones' => 'programacion']);
        Route::apiResource('salarios',         SalarioController::class)->parameters(['salarios' => 'salario']);
        Route::apiResource('participantes',    ParticipanteController::class)->parameters(['participantes' => 'participante']);
        Route::apiResource('auxilios',         AuxilioController::class)->parameters(['auxilios' => 'auxilio']);
        Route::apiResource('rutas',            RutaController::class)->parameters(['rutas' => 'ruta']);
        Route::apiResource('reprogramaciones', ReprogramacionController::class)->parameters(['reprogramaciones' => 'reprogramacion']);
        Route::apiResource('legalizaciones',   LegalizacionController::class)->parameters(['legalizaciones' => 'legalizacion']);
        Route::apiResource('fechas',           FechaController::class)->parameters(['fechas' => 'fecha']);
        Route::apiResource('creaciones',       CreacionController::class)->parameters(['creaciones' => 'creacion']);
        Route::apiResource('ajustes',          AjusteController::class)->parameters(['ajustes' => 'ajuste']);

        // Ruta de prueba
        Route::post('_echo', function (Request $r) {
            return response()->json([
                'all'   => $r->all(),
                'json'  => $r->isJson() ? $r->json()->all() : null,
                'query' => $r->query(),
            ]);
        });
    });
