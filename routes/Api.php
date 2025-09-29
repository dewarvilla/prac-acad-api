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

Route::prefix('v1')->group(function() {

        Route::post('catalogos/bulk',        [CatalogoController::class, 'storeBulk'])->name('catalogos.bulk');
        Route::apiResource('catalogos', CatalogoController::class)->parameters(['catalogos' => 'catalogo']);
        Route::post('catalogos/bulk-delete', [CatalogoController::class, 'destroyBulk'])->name('catalogos.bulk-delete');

        Route::apiResource('programaciones',   ProgramacionController::class)->parameters(['programaciones' => 'programacion']);
        Route::post('programaciones/bulk-delete', [ProgramacionController::class, 'destroyBulk'])->name('programaciones.bulk-delete');

        Route::apiResource('salarios',         SalarioController::class)->parameters(['salarios' => 'salario']);
        Route::post('salarios/bulk-delete', [SalarioController::class, 'destroyBulk'])->name('salarios.bulk-delete');

        Route::apiResource('participantes',    ParticipanteController::class)->parameters(['participantes' => 'participante']);
        Route::post('participantes/bulk-delete', [ParticipanteController::class, 'destroyBulk'])->name('participantes.bulk-delete');

        Route::apiResource('auxilios',         AuxilioController::class)->parameters(['auxilios' => 'auxilio']);
        Route::post('auxilios/bulk-delete', [AuxilioController::class, 'destroyBulk'])->name('auxilios.bulk-delete');

        Route::apiResource('rutas',            RutaController::class)->parameters(['rutas' => 'ruta']);
        Route::post('rutas/bulk-delete', [RutaController::class, 'destroyBulk'])->name('rutas.bulk-delete');

        Route::apiResource('reprogramaciones', ReprogramacionController::class)->parameters(['reprogramaciones' => 'reprogramacion']);
        Route::post('reprogramaciones/bulk-delete', [ReprogramacionController::class, 'destroyBulk'])->name('reprogramaciones.bulk-delete');

        Route::apiResource('legalizaciones',   LegalizacionController::class)->parameters(['legalizaciones' => 'legalizacion']);
        Route::post('legalizaciones/bulk-delete', [LegalizacionController::class, 'destroyBulk'])->name('legalizaciones.bulk-delete');

        Route::apiResource('fechas',           FechaController::class)->parameters(['fechas' => 'fecha']);
        Route::post('fechas/bulk-delete', [FechaController::class, 'destroyBulk'])->name('fechas.bulk-delete');

        Route::apiResource('creaciones',       CreacionController::class)->parameters(['creaciones' => 'creacion']);
        Route::post('creaciones/bulk-delete', [CreacionController::class, 'destroyBulk'])->name('creaciones.bulk-delete');

        Route::apiResource('ajustes',          AjusteController::class)->parameters(['ajustes' => 'ajuste']);
        Route::post('ajustes/bulk-delete', [AjusteController::class, 'destroyBulk'])->name('ajustes.bulk-delete');

        // Ruta de prueba
        /* Route::post('_echo', function (Request $r) {
            return response()->json([
                'all'   => $r->all(),
                'json'  => $r->isJson() ? $r->json()->all() : null,
                'query' => $r->query(),
            ]);
        }); */
    });
