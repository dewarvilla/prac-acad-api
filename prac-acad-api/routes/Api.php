<?php

use Illuminate\Support\Facades\Route;

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

Route::prefix('v1')->group(function () {

    // ---- Catálogos (bulk + resource una sola vez)
    Route::post('catalogos/bulk',        [CatalogoController::class, 'storeBulk'])->name('catalogos.bulk');
    Route::post('catalogos/bulk-delete', [CatalogoController::class, 'destroyBulk'])->name('catalogos.bulk-delete');
    Route::apiResource('catalogos', CatalogoController::class)
        ->parameters(['catalogos' => 'catalogo']);

    // Resto de recursos
    Route::apiResource('programaciones', ProgramacionController::class)->parameters(['programaciones' => 'programacion']);
    Route::apiResource('salarios', SalarioController::class)->parameters(['salarios' => 'salario']);
    Route::apiResource('participantes', ParticipanteController::class)->parameters(['participantes' => 'participante']);
    Route::apiResource('auxilios', AuxilioController::class)->parameters(['auxilios' => 'auxilio']);
    Route::apiResource('rutas', RutaController::class)->parameters(['rutas' => 'ruta']);
    Route::apiResource('reprogramaciones', ReprogramacionController::class)->parameters(['reprogramaciones' => 'reprogramacion']);
    Route::apiResource('legalizaciones', LegalizacionController::class)->parameters(['legalizaciones' => 'legalizacion']);
    Route::apiResource('fechas', FechaController::class)->parameters(['fechas' => 'fecha']);
    Route::apiResource('creaciones', CreacionController::class)->parameters(['creaciones' => 'creacion']);
    Route::apiResource('ajustes', AjusteController::class)->parameters(['ajustes' => 'ajuste']);

    // Ruta de prueba del middleware (borra cuando termines)
    Route::post('_echo', fn (Illuminate\Http\Request $r) =>
        response()->json(['all' => $r->all(), 'json' => $r->json()->all(), 'query' => $r->query()])
    );
});