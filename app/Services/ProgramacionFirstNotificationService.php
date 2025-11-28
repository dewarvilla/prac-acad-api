<?php

namespace App\Services;

use App\Models\Programacion;
use App\Models\User;
use App\Notifications\ProgramacionDecisionNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class ProgramacionFirstNotificationService
{
    public function notifyFirstApprover(Programacion $p): void
    {
        $p->loadMissing('creacion:id,nivel_academico');

        if (!$p->creacion) {
            Log::warning('Programación sin creación asociada al notificar primer aprobador', [
                'programacion_id' => $p->id,
                'creacion_id'     => $p->creacion_id ?? null,
            ]);
            return;
        }

        $nivelRaw = $p->creacion->nivel_academico;
        $nivel    = mb_strtolower(trim($nivelRaw));

        Log::info('Determinando primer aprobador', [
            'programacion_id'   => $p->id,
            'nivel_academico'   => $nivelRaw,
            'nivel_normalizado' => $nivel,
        ]);

        if (str_contains($nivel, 'postgrad')) {
            $actorKey = 'postg';
            $roles    = ['coordinador_postgrados'];
        } elseif (str_contains($nivel, 'pregrad')) {
            $actorKey = 'depart';
            $roles    = ['jefe_departamento'];
        } else {
            Log::warning('Nivel académico no reconocido para primer aprobador', [
                'programacion_id' => $p->id,
                'nivel_academico' => $nivelRaw,
            ]);
            return;
        }

        $users = User::role($roles)->get();

        if ($users->isEmpty()) {
            Log::warning('No se encontraron usuarios para primer aprobador', [
                'programacion_id' => $p->id,
                'nivel_academico' => $nivelRaw,
                'roles'           => $roles,
            ]);
            return;
        }

        Log::info('Enviando notificaciones a primer aprobador', [
            'programacion_id' => $p->id,
            'nivel_academico' => $nivelRaw,
            'roles'           => $roles,
            'user_ids'        => $users->pluck('id')->all(),
        ]);

        Notification::send(
            $users,
            new ProgramacionDecisionNotification($p, $actorKey, 'aprobada', 'siguiente')
        );
    }
}
