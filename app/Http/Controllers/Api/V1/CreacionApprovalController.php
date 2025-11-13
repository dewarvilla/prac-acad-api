<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Creacion;
use Illuminate\Http\Request;

class CreacionApprovalController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:creaciones.aprobar.comite_acreditacion')->only('approveComiteAcreditacion');
        $this->middleware('permission:creaciones.rechazar.comite_acreditacion')->only('rejectComiteAcreditacion');

        $this->middleware('permission:creaciones.aprobar.consejo_facultad')->only('approveConsejoFacultad');
        $this->middleware('permission:creaciones.rechazar.consejo_facultad')->only('rejectConsejoFacultad');

        $this->middleware('permission:creaciones.aprobar.consejo_academico')->only('approveConsejoAcademico');
        $this->middleware('permission:creaciones.rechazar.consejo_academico')->only('rejectConsejoAcademico');
    }

    /* ===== Aprobaciones ===== */
    public function approveComiteAcreditacion(Request $r, Creacion $creacion)
    { return $this->approve($r, $creacion, 'comite_acreditacion'); }

    public function approveConsejoFacultad(Request $r, Creacion $creacion)
    { return $this->approve($r, $creacion, 'consejo_facultad'); }

    public function approveConsejoAcademico(Request $r, Creacion $creacion)
    { return $this->approve($r, $creacion, 'consejo_academico'); }

    /* ===== Rechazos ===== */
    public function rejectComiteAcreditacion(Request $r, Creacion $creacion)
    { return $this->reject($r, $creacion, 'comite_acreditacion'); }

    public function rejectConsejoFacultad(Request $r, Creacion $creacion)
    { return $this->reject($r, $creacion, 'consejo_facultad'); }

    public function rejectConsejoAcademico(Request $r, Creacion $creacion)
    { return $this->reject($r, $creacion, 'consejo_academico'); }

    protected function approve(Request $r, Creacion $c, string $actorKey)
    {
        if (in_array($c->estado_creacion, ['rechazada','aprobada'], true)) {
            return response()->json([
                'ok' => false,
                'message' => "La creación ya está '{$c->estado_creacion}'."
            ], 409);
        }

        $column = $this->estadoColumn($actorKey);
        if (!$column) {
            return response()->json(['ok'=>false,'message'=>'Actor inválido'], 422);
        }

        if ($c->{$column} !== 'pendiente') {
            return response()->json([
                'ok' => false,
                'message' => "La etapa '{$column}' ya fue marcada como '{$c->{$column}}'."
            ], 409);
        }

        [$flowKeys, $flowCols] = $this->flowFor($c);
        if (!in_array($actorKey, $flowKeys, true)) {
            return response()->json([
                'ok' => false,
                'message' => "La etapa '{$actorKey}' no aplica para el flujo de esta creación."
            ], 422);
        }
        $idx = array_search($actorKey, $flowKeys, true);
        for ($i = 0; $i < $idx; $i++) {
            $prevCol = $flowCols[$i];
            if ($c->{$prevCol} !== 'aprobada') {
                return response()->json([
                    'ok' => false,
                    'message' => "No puede aprobar aún. La etapa previa '{$prevCol}' no está aprobada."
                ], 409);
            }
        }

        $c->{$column} = 'aprobada';
        $c->usuariomodificacion = auth()->id() ?? 0;
        $c->ipmodificacion = $r->ip();
        $c->save();

        if ($this->allRequiredApproved($c)) {
            $c->estado_creacion = 'aprobada';
            $c->save();
        } else {
        }

        $this->logDecision($c, $actorKey, 'aprobada', null);

        return response()->json([
            'ok'   => true,
            'data' => $c->fresh(),
        ]);
    }

    protected function reject(Request $r, Creacion $c, string $actorKey)
    {
        if (in_array($c->estado_creacion, ['rechazada','aprobada'], true)) {
            return response()->json([
                'ok' => false,
                'message' => "La creación ya está '{$c->estado_creacion}'."
            ], 409);
        }

        $column = $this->estadoColumn($actorKey);
        if (!$column) {
            return response()->json(['ok'=>false,'message'=>'Actor inválido'], 422);
        }

        if ($c->{$column} !== 'pendiente') {
            return response()->json([
                'ok' => false,
                'message' => "La etapa '{$column}' ya fue marcada como '{$c->{$column}}'."
            ], 409);
        }

        $data = $r->validate([
            'justificacion' => ['required','string','min:5'],
        ]);

        $c->{$column} = 'rechazada';
        $c->estado_creacion = 'rechazada';
        $c->usuariomodificacion = auth()->id() ?? 0;
        $c->ipmodificacion = $r->ip();
        $c->save();

        $this->logDecision($c, $actorKey, 'rechazada', $data['justificacion'] ?? null);

        return response()->json([
            'ok'   => true,
            'data' => $c->fresh(),
        ]);
    }

    protected function estadoColumn(string $actorKey): ?string
    {
        return [
            'comite_acreditacion' => 'estado_comite_acreditacion',
            'consejo_facultad'    => 'estado_consejo_facultad',
            'consejo_academico'   => 'estado_consejo_academico',
        ][$actorKey] ?? null;
    }

    protected function flowFor(Creacion $c): array
    {
        $keys = ['comite_acreditacion', 'consejo_facultad', 'consejo_academico'];
        $cols = array_map(fn($k) => $this->estadoColumn($k), $keys);
        return [$keys, $cols];
    }

    protected function allRequiredApproved(Creacion $c): bool
    {
        [, $flowCols] = $this->flowFor($c);
        foreach ($flowCols as $col) {
            if ($c->{$col} !== 'aprobada') {
                return false;
            }
        }
        return true;
    }

    protected function logDecision(Creacion $c, string $actorKey, string $decision, ?string $justificacion)
    {
        if (class_exists(\App\Models\CreacionDecision::class)) {
            \App\Models\CreacionDecision::create([
                'creacion_id'    => $c->id,
                'actor'          => $actorKey,
                'decision'       => $decision,
                'justificacion'  => $justificacion,
                'user_id'        => auth()->id() ?? null,
                'ip'             => request()->ip(),
            ]);
        }
    }
}
