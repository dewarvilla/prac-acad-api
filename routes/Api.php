<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CatalogoController;
use App\Http\Controllers\Api\V1\ProgramacionController;
use App\Http\Controllers\Api\V1\SalarioController;
use App\Http\Controllers\Api\V1\ParticipanteController;
use App\Http\Controllers\Api\V1\AuxilioController;
use App\Http\Controllers\Api\V1\RutaController;
use App\Http\Controllers\Api\V1\RutapeajeController;
use App\Http\Controllers\Api\V1\RutapeajesSyncController;
use App\Http\Controllers\Api\V1\ReprogramacionController;
use App\Http\Controllers\Api\V1\LegalizacionController;
use App\Http\Controllers\Api\V1\FechaController;
use App\Http\Controllers\Api\V1\CreacionController;
use App\Http\Controllers\Api\V1\AjusteController;
use App\Http\Controllers\Api\V1\UsageController;
use App\Http\Controllers\Api\V1\RoutesComputeController;
use App\Http\Controllers\Api\V1\ProgramacionApprovalController;
use App\Http\Controllers\Api\V1\CreacionApprovalController;

Route::prefix('v1')->group(function () {

    Route::middleware('auth:sanctum')->group(function () {
        
        /* ==================== CATALOGOS ==================== */
        Route::apiResource('catalogos', CatalogoController::class)->parameters(['catalogos' => 'catalogo']);
        Route::post('catalogos/bulk', [CatalogoController::class, 'storeBulk'])->name('catalogos.bulk.store');
        Route::post('catalogos/bulk-delete', [CatalogoController::class, 'destroyBulk'])->name('catalogos.bulk.delete');

        /* ==================== PROGRAMACIONES ==================== */
        Route::apiResource('programaciones', ProgramacionController::class)->parameters(['programaciones' => 'programacion']);
        Route::post('programaciones/bulk-delete', [ProgramacionController::class, 'destroyBulk'])->name('programaciones.bulk.delete');

        /* ----- Aprobaciones/Rechazos Programaciones ----- */
        Route::prefix('programaciones/{programacion}')->whereNumber('programacion')->group(function () {
            Route::post('aprobar/departamento', [ProgramacionApprovalController::class, 'approveDepartamento'])->name('programaciones.approvals.departamento.approve');
            Route::post('rechazar/departamento', [ProgramacionApprovalController::class, 'rejectDepartamento'])->name('programaciones.approvals.departamento.reject');

            Route::post('aprobar/postgrados', [ProgramacionApprovalController::class, 'approvePostgrados'])->name('programaciones.approvals.postgrados.approve');
            Route::post('rechazar/postgrados', [ProgramacionApprovalController::class, 'rejectPostgrados'])->name('programaciones.approvals.postgrados.reject');

            Route::post('aprobar/decano', [ProgramacionApprovalController::class, 'approveDecano'])->name('programaciones.approvals.decano.approve');
            Route::post('rechazar/decano', [ProgramacionApprovalController::class, 'rejectDecano'])->name('programaciones.approvals.decano.reject');

            Route::post('aprobar/jefe-postgrados', [ProgramacionApprovalController::class, 'approveJefePostgrados'])->name('programaciones.approvals.jefe_postgrados.approve');
            Route::post('rechazar/jefe-postgrados', [ProgramacionApprovalController::class, 'rejectJefePostgrados'])->name('programaciones.approvals.jefe_postgrados.reject');

            Route::post('aprobar/vicerrectoria', [ProgramacionApprovalController::class, 'approveVicerrectoria'])->name('programaciones.approvals.vicerrectoria.approve');
            Route::post('rechazar/vicerrectoria', [ProgramacionApprovalController::class, 'rejectVicerrectoria'])->name('programaciones.approvals.vicerrectoria.reject');
        });

        /* ==================== SALARIOS ==================== */
        Route::apiResource('salarios', SalarioController::class)->parameters(['salarios' => 'salario']);
        Route::post('salarios/bulk-delete', [SalarioController::class, 'destroyBulk'])->name('salarios.bulk.delete');

        /* ==================== FECHAS ==================== */
        Route::apiResource('fechas', FechaController::class)->parameters(['fechas' => 'fecha']);
        Route::post('fechas/bulk-delete', [FechaController::class, 'destroyBulk'])->name('fechas.bulk.delete');

        /* ==================== CREACIONES ==================== */
        Route::apiResource('creaciones', CreacionController::class)->parameters(['creaciones' => 'creacion']);
        Route::post('creaciones/bulk-delete', [CreacionController::class, 'destroyBulk'])->name('creaciones.bulk.delete');

        /* ----- Aprobaciones/Rechazos Creaciones ----- */
        Route::prefix('creaciones/{creacion}')->whereNumber('creacion')->group(function () {
            Route::post('aprobar/comite-acreditacion', [CreacionApprovalController::class, 'approveComiteAcreditacion'])->name('creaciones.approvals.comite_acreditacion.approve');
            Route::post('rechazar/comite-acreditacion', [CreacionApprovalController::class, 'rejectComiteAcreditacion'])->name('creaciones.approvals.comite_acreditacion.reject');

            Route::post('aprobar/consejo-facultad', [CreacionApprovalController::class, 'approveConsejoFacultad'])->name('creaciones.approvals.consejo-facultad.approve');
            Route::post('rechazar/consejo-facultad', [CreacionApprovalController::class, 'rejectConsejoFacultad'])->name('creaciones.approvals.consejo-facultad.reject');

            Route::post('aprobar/consejo-academico', [CreacionApprovalController::class, 'approveConsejoAcademico'])->name('creaciones.approvals.consejo-academico.approve');
            Route::post('rechazar/consejo-academico', [CreacionApprovalController::class, 'rejectConsejoAcademico'])->name('creaciones.approvals.consejo-academico.reject');
        });

        /* ==================== PARTICIPANTES ==================== */
        Route::apiResource('participantes', ParticipanteController::class)->parameters(['participantes' => 'participante']);
        Route::post('participantes/bulk-delete', [ParticipanteController::class, 'destroyBulk'])->name('participantes.bulk.delete');

        /* ==================== AUXILIOS ==================== */
        Route::apiResource('auxilios', AuxilioController::class)->parameters(['auxilios' => 'auxilio']);
        Route::post('auxilios/bulk-delete', [AuxilioController::class, 'destroyBulk'])->name('auxilios.bulk.delete');

        /* ==================== RUTAS ==================== */
        Route::apiResource('rutas', RutaController::class)->parameters(['rutas' => 'ruta']);
        Route::post('rutas/bulk-delete', [RutaController::class, 'destroyBulk'])->name('rutas.bulk.delete');

        /* === RUTAS → PEAJES === */
        Route::get('rutas/{ruta}/peajes', [RutapeajeController::class, 'indexByRuta'])->name('rutapeajes.indexByRuta')->whereNumber('ruta');
        Route::post('rutapeajes', [RutapeajeController::class, 'store'])->name('rutapeajes.store');
        Route::put('rutapeajes/{rutapeaje}', [RutapeajeController::class, 'update'])->name('rutapeajes.update')->whereNumber('rutapeaje');
        Route::delete('rutapeajes/{rutapeaje}', [RutapeajeController::class, 'destroy'])->name('rutapeajes.destroy')->whereNumber('rutapeaje');

        // Sincronizar desde datos.gov.co y calcular totales
        Route::post('rutas/{ruta}/peajes/sync', [RutapeajesSyncController::class, 'recalcular'])->name('rutapeajes.sync')->whereNumber('ruta');
        Route::get('rutas/{ruta}/peajes/total', [RutapeajesSyncController::class, 'totalCategoria'])->name('rutapeajes.total')->whereNumber('ruta');

        /* ==================== REPROGRAMACIONES ==================== */
        Route::apiResource('reprogramaciones', ReprogramacionController::class)->parameters(['reprogramaciones' => 'reprogramacion']);
        Route::post('reprogramaciones/bulk-delete', [ReprogramacionController::class, 'destroyBulk'])->name('reprogramaciones.bulk.delete');

        /* ==================== LEGALIZACIONES ==================== */
        Route::apiResource('legalizaciones', LegalizacionController::class)->parameters(['legalizaciones' => 'legalizacion']);
        Route::post('legalizaciones/bulk-delete', [LegalizacionController::class, 'destroyBulk'])->name('legalizaciones.bulk.delete');

        /* ==================== AJUSTES ==================== */
        Route::apiResource('ajustes', AjusteController::class)->parameters(['ajustes' => 'ajuste']);
        Route::post('ajustes/bulk-delete', [AjusteController::class, 'destroyBulk'])->name('ajustes.bulk.delete');

        /* ==================== Usage / Compute ==================== */
        Route::post('usage/routes/preflight', [UsageController::class, 'preflight'])->name('usage.routes.preflight');
        Route::get('usage/routes/stats', [UsageController::class, 'stats'])->name('usage.routes.stats');

        Route::post('compute-route', [RoutesComputeController::class, 'compute'])->name('compute.route');
    });
});

/* use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

Route::post('/v1/token/login', function (Request $request) {
    $credentials = $request->validate([
        'email'    => ['required','email'],
        'password' => ['required'],
    ]);

    if (!Auth::attempt($credentials)) {
        return response()->json(['message' => 'Credenciales inválidas'], 422);
    }

    $user = User::where('email', $request->email)->firstOrFail();

    $token = $user->createToken('postman', ['*'])->plainTextToken;

    return response()->json([
        'token' => $token,
        'user'  => $user->only('id','name','email')
    ]);
})->name('token.login'); */

