<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\V1\PracticaController;
use App\Http\Controllers\Api\V1\SalarioController;
use App\Http\Controllers\Api\V1\ParticipanteController;
use App\Http\Controllers\Api\V1\AuxilioController;
use App\Http\Controllers\Api\V1\RutaController;
use App\Http\Controllers\Api\V1\ReprogramacionController;
use App\Http\Controllers\Api\V1\LegalizacionController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API v1 (protegida con Sanctum)
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {

    // Recursos principales
    Route::apiResource('practicas', PracticaController::class);
    Route::apiResource('salarios', SalarioController::class)->only(['index','show','store','update']);
    Route::apiResource('participantes', ParticipanteController::class);
    Route::apiResource('auxilios', AuxilioController::class);
    Route::apiResource('rutas', RutaController::class);
    Route::apiResource('reprogramaciones', ReprogramacionController::class);
    Route::apiResource('legalizaciones', LegalizacionController::class);
    Route::apiResource('fechas', FechaController::class);

    // carga masiva de participantes
    Route::post('participantes/bulk', [ParticipanteController::class, 'bulkStore'])
        ->name('participantes.bulk-store');
});