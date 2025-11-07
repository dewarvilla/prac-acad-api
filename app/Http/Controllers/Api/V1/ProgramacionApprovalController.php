<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Programacion;
use Illuminate\Http\Request;

class ProgramacionApprovalController extends Controller
{
    // ======= Aprobaciones =======
    public function approveDepartamento(Request $r, Programacion $programacion)
    {
        return $this->approve($r, $programacion, 'depart');
    }
    public function approvePostgrados(Request $r, Programacion $programacion)
    {
        return $this->approve($r, $programacion, 'postg');
    }
    public function approveDecano(Request $r, Programacion $programacion)
    {
        return $this->approve($r, $programacion, 'decano');
    }
    public function approveJefePostgrados(Request $r, Programacion $programacion)
    {
        return $this->approve($r, $programacion, 'jefe_postg');
    }
    public function approveVicerrectoria(Request $r, Programacion $programacion)
    {
        return $this->approve($r, $programacion, 'vice');
    }

    // ======= Rechazos =======
    public function rejectDepartamento(Request $r, Programacion $programacion)
    {
        return $this->reject($r, $programacion, 'depart');
    }
    public function rejectPostgrados(Request $r, Programacion $programacion)
    {
        return $this->reject($r, $programacion, 'postg');
    }
    public function rejectDecano(Request $r, Programacion $programacion)
    {
        return $this->reject($r, $programacion, 'decano');
    }
    public function rejectJefePostgrados(Request $r, Programacion $programacion)
    {
        return $this->reject($r, $programacion, 'jefe_postg');
    }
    public function rejectVicerrectoria(Request $r, Programacion $programacion)
    {
        return $this->reject($r, $programacion, 'vice');
    }

    // ======= Core =======
    protected function approve(Request $r, Programacion $p, string $actorKey)
    {
        if (in_array($p->estado_practica, ['rechazada', 'aprobada'], true)) {
            return response()->json([
                'ok' => false,
                'message' => "La programación ya está '{$p->estado_practica}'."
            ], 409);
        }

        $column = $this->estadoColumn($actorKey);
        if (!$column) {
            return response()->json(['ok'=>false,'message'=>'Actor inválido'], 422);
        }

        if ($p->{$column} !== 'pendiente') {
            return response()->json([
                'ok' => false,
                'message' => "La etapa '{$column}' ya fue marcada como '{$p->{$column}}'."
            ], 409);
        }

        // === NUEVO: validar flujo y prerequisitos ===
        [$flowKeys, $flowCols] = $this->flowFor($p);           
        if (!in_array($actorKey, $flowKeys, true)) {
            return response()->json([
                'ok' => false,
                'message' => "La etapa '{$actorKey}' no aplica para el flujo de esta práctica."
            ], 422);
        }

        $idx = array_search($actorKey, $flowKeys, true);
        // Todas las etapas anteriores deben estar 'aprobada'
        for ($i = 0; $i < $idx; $i++) {
            $prevCol = $flowCols[$i];
            if ($p->{$prevCol} !== 'aprobada') {
                return response()->json([
                    'ok' => false,
                    'message' => "No puede aprobar aún. La etapa previa '{$prevCol}' no está aprobada."
                ], 409);
            }
        }

        // Transición: aprobar etapa actual
        $p->{$column} = 'aprobada';
        $p->usuariomodificacion = auth()->id() ?? 0;
        $p->ipmodificacion = $r->ip();
        $p->save();

        // Si todas las requeridas están aprobadas => aprobar global
        if ($this->allRequiredApproved($p)) {
            $p->estado_practica = 'aprobada';
            $p->save();
            // TODO notificar al docente
        } else {
            // TODO notificar al siguiente actor del flujo
        }

        $this->logDecision($p, $actorKey, 'aprobada', null);

        return response()->json([
            'ok' => true,
            'data' => $p->fresh(),
        ]);
    }

    protected function reject(Request $r, Programacion $p, string $actorKey)
    {
        if (in_array($p->estado_practica, ['rechazada', 'aprobada'], true)) {
            return response()->json([
                'ok' => false,
                'message' => "La programación ya está '{$p->estado_practica}'."
            ], 409);
        }

        $column = $this->estadoColumn($actorKey);
        if (!$column) {
            return response()->json(['ok'=>false,'message'=>'Actor inválido'], 422);
        }

        if ($p->{$column} !== 'pendiente') {
            return response()->json([
                'ok' => false,
                'message' => "La etapa '{$column}' ya fue marcada como '{$p->{$column}}'."
            ], 409);
        }

        $data = $r->validate([
            'justificacion' => ['required','string','min:5'],
        ]);

        $p->{$column} = 'rechazada';
        $p->estado_practica = 'rechazada';
        $p->usuariomodificacion = auth()->id() ?? 0;
        $p->ipmodificacion = $r->ip();
        $p->save();

        $this->logDecision($p, $actorKey, 'rechazada', $data['justificacion'] ?? null);

        // TODO: notificar a docente con justificación
        return response()->json([
            'ok' => true,
            'data' => $p->fresh(),
        ]);
    }

    protected function estadoColumn(string $actorKey): ?string
    {
        return [
            'depart'     => 'estado_depart',
            'postg'      => 'estado_postg',
            'decano'     => 'estado_decano',
            'jefe_postg' => 'estado_jefe_postg',
            'vice'       => 'estado_vice',
        ][$actorKey] ?? null;
    }

    protected function flowFor(Programacion $p): array
{
    // Garantiza que la relación esté cargada
    $p->loadMissing('creacion:id,nivel_academico');

    if (!$p->creacion) {
        // si este caso es imposible por modelo de negocio, mejor 500/409 explícito
        abort(409, 'La programación no tiene una creación asociada.');
    }

    $nivel = strtolower($p->creacion->nivel_academico); // 'pregrado' | 'postgrado'

    if ($nivel === 'postgrado') {
        $keys = ['postg', 'jefe_postg', 'vice'];
    } else if ($nivel === 'pregrado') {
        $keys = ['depart', 'decano', 'vice'];
    } else {
        // por si en el futuro alguien mete un valor inválido
        abort(422, "Nivel académico inválido: {$nivel}");
    }

    $cols = array_map(fn($k) => $this->estadoColumn($k), $keys);

    return [$keys, $cols];
}

    /** ¿Todas las etapas requeridas aprobadas? ahora respeta el flujo del nivel */
    protected function allRequiredApproved(Programacion $p): bool
    {
        [, $flowCols] = $this->flowFor($p);
        foreach ($flowCols as $col) {
            if ($p->{$col} !== 'aprobada') {
                return false;
            }
        }
        return true;
    }

    protected function logDecision(Programacion $p, string $actorKey, string $decision, ?string $justificacion)
    {
        if (class_exists(\App\Models\ProgramacionDecision::class)) {
            \App\Models\ProgramacionDecision::create([
                'programacion_id' => $p->id,
                'actor'           => $actorKey,
                'decision'        => $decision,
                'justificacion'   => $justificacion,
                'user_id'         => auth()->id() ?? null,
                'ip'              => request()->ip(),
            ]);
        }
    }
}

