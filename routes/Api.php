<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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
use App\Http\Controllers\Api\V1\RutapeajeController;
use App\Http\Controllers\Api\V1\RutapeajesSyncController;
use App\Http\Controllers\Api\V1\UsageController;
use App\Http\Controllers\Api\V1\RoutesComputeController;
use App\Http\Controllers\Api\V1\ProgramacionApprovalController;
use App\Http\Controllers\Api\V1\AuthController;

Route::prefix('v1')->group(function() {

    /* ==================== CATALOGOS ==================== */
    Route::post('catalogos/bulk', [CatalogoController::class, 'storeBulk'])->name('catalogos.bulk');
    Route::apiResource('catalogos', CatalogoController::class)->parameters(['catalogos' => 'catalogo']);
    Route::post('catalogos/bulk-delete', [CatalogoController::class, 'destroyBulk'])->name('catalogos.bulk-delete');

    /* ==================== PROGRAMACIONES ==================== */
    Route::apiResource('programaciones', ProgramacionController::class)->parameters(['programaciones' => 'programacion']);
    Route::post('programaciones/bulk-delete', [ProgramacionController::class, 'destroyBulk'])->name('programaciones.bulk-delete');

    /* ==================== SALARIOS ==================== */
    Route::apiResource('salarios', SalarioController::class)->parameters(['salarios' => 'salario']);
    Route::post('salarios/bulk-delete', [SalarioController::class, 'destroyBulk'])->name('salarios.bulk-delete');

    /* ==================== PARTICIPANTES ==================== */
    Route::apiResource('participantes', ParticipanteController::class)->parameters(['participantes' => 'participante']);
    Route::post('participantes/bulk-delete', [ParticipanteController::class, 'destroyBulk'])->name('participantes.bulk-delete');

    /* ==================== AUXILIOS ==================== */
    Route::apiResource('auxilios', AuxilioController::class)->parameters(['auxilios' => 'auxilio']);
    Route::post('auxilios/bulk-delete', [AuxilioController::class, 'destroyBulk'])->name('auxilios.bulk-delete');

    /* ==================== RUTAS ==================== */
    Route::apiResource('rutas', RutaController::class)->parameters(['rutas' => 'ruta']);
    Route::post('rutas/bulk-delete', [RutaController::class, 'destroyBulk'])->name('rutas.bulk-delete');

    /* === RUTAS → PEAJES === */
    Route::get('rutas/{ruta}/peajes', [RutapeajeController::class, 'indexByRuta'])->name('rutapeajes.indexByRuta');
    Route::post('rutapeajes', [RutapeajeController::class, 'store'])->name('rutapeajes.store');
    Route::put('rutapeajes/{rutapeaje}', [RutapeajeController::class, 'update'])->name('rutapeajes.update');
    Route::delete('rutapeajes/{rutapeaje}', [RutapeajeController::class, 'destroy'])->name('rutapeajes.destroy');

    // sincronizar desde datos.gov.co y calcular totales
    Route::post('rutas/{ruta}/peajes/sync', [RutapeajesSyncController::class, 'recalcular'])->name('rutapeajes.sync');
    Route::get('rutas/{ruta}/peajes/total', [RutapeajesSyncController::class, 'totalCategoria'])->name('rutapeajes.total');

    /* ==================== REPROGRAMACIONES ==================== */
    Route::apiResource('reprogramaciones', ReprogramacionController::class)->parameters(['reprogramaciones' => 'reprogramacion']);
    Route::post('reprogramaciones/bulk-delete', [ReprogramacionController::class, 'destroyBulk'])->name('reprogramaciones.bulk-delete');

    /* ==================== LEGALIZACIONES ==================== */
    Route::apiResource('legalizaciones', LegalizacionController::class)->parameters(['legalizaciones' => 'legalizacion']);
    Route::post('legalizaciones/bulk-delete', [LegalizacionController::class, 'destroyBulk'])->name('legalizaciones.bulk-delete');

    /* ==================== FECHAS ==================== */
    Route::apiResource('fechas', FechaController::class)->parameters(['fechas' => 'fecha']);
    Route::post('fechas/bulk-delete', [FechaController::class, 'destroyBulk'])->name('fechas.bulk-delete');

    /* ==================== CREACIONES ==================== */
    Route::apiResource('creaciones', CreacionController::class)->parameters(['creaciones' => 'creacion']);
    Route::post('creaciones/bulk-delete', [CreacionController::class, 'destroyBulk'])->name('creaciones.bulk-delete');

    /* ==================== AJUSTES ==================== */
    Route::apiResource('ajustes', AjusteController::class)->parameters(['ajustes' => 'ajuste']);
    Route::post('ajustes/bulk-delete', [AjusteController::class, 'destroyBulk'])->name('ajustes.bulk-delete');

    /* ==================== Usage ==================== */
    Route::post('/usage/routes/preflight', [UsageController::class, 'preflight']);
    Route::get('/usage/routes/stats', [UsageController::class, 'stats']);

    /* ==================== Compute para integracion con maps ==================== */
    Route::post('compute-route', [RoutesComputeController::class, 'compute']);

    Route::middleware(['auth:sanctum'])->group(function () {
    /* ===== Departamento ===== */
    Route::post('programaciones/{programacion}/aprobar/departamento', [ProgramacionApprovalController::class, 'approveDepartamento'])
        ->middleware('permission:programaciones.aprobar.departamento');
    Route::post('programaciones/{programacion}/rechazar/departamento', [ProgramacionApprovalController::class, 'rejectDepartamento'])
        ->middleware('permission:programaciones.rechazar.departamento');

    /* ===== Postgrados (Coordinación) ===== */
    Route::post('programaciones/{programacion}/aprobar/postgrados', [ProgramacionApprovalController::class, 'approvePostgrados'])
        ->middleware('permission:programaciones.aprobar.postgrados');
    Route::post('programaciones/{programacion}/rechazar/postgrados', [ProgramacionApprovalController::class, 'rejectPostgrados'])
        ->middleware('permission:programaciones.rechazar.postgrados');

    /* ===== Decano ===== */
    Route::post('programaciones/{programacion}/aprobar/decano', [ProgramacionApprovalController::class, 'approveDecano'])
        ->middleware('permission:programaciones.aprobar.decano');
    Route::post('programaciones/{programacion}/rechazar/decano', [ProgramacionApprovalController::class, 'rejectDecano'])
        ->middleware('permission:programaciones.rechazar.decano');

    /* ===== Jefe de Oficina de Postgrados ===== */
    Route::post('programaciones/{programacion}/aprobar/jefe-postgrados', [ProgramacionApprovalController::class, 'approveJefePostgrados'])
        ->middleware('permission:programaciones.aprobar.jefe_postgrados');
    Route::post('programaciones/{programacion}/rechazar/jefe-postgrados', [ProgramacionApprovalController::class, 'rejectJefePostgrados'])
        ->middleware('permission:programaciones.rechazar.jefe_postgrados');

    /* ===== Vicerrectoría Académica ===== */
    Route::post('programaciones/{programacion}/aprobar/vicerrectoria', [ProgramacionApprovalController::class, 'approveVicerrectoria'])
        ->middleware('permission:programaciones.aprobar.vicerrectoria');
    Route::post('programaciones/{programacion}/rechazar/vicerrectoria', [ProgramacionApprovalController::class, 'rejectVicerrectoria'])
        ->middleware('permission:programaciones.rechazar.vicerrectoria');
    });

    Route::post('login',  [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me',      [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
    });

    /* ==================== PRUEBA LOCAL ==================== */
 /*
    Route::get('peajes/test', function () {
        $url = 'https://www.datos.gov.co/api/v3/views/68qj-5xux/query.json';

        try {
            $response = Http::withHeaders([
                'X-App-Token' => env('SODA_APP_TOKEN'),
            ])->get($url, [
                '$limit' => 5,
            ]);

            if ($response->failed()) {
                return response()->json([
                    'ok' => false,
                    'status' => $response->status(),
                    'message' => $response->json(),
                ], $response->status());
            }

            return response()->json([
                'ok' => true,
                'data' => $response->json(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    });
   
    Route::post('_echo', function (Request $r) {
        return response()->json([
            'all'   => $r->all(),
            'json'  => $r->isJson() ? $r->json()->all() : null,
            'query' => $r->query(),
        ]);
    });
    */
});
