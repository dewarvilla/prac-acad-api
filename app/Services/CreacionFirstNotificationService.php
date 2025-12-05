<?php

namespace App\Services;

use App\Models\Creacion;
use App\Models\User;
use App\Notifications\CreacionDecisionNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class CreacionFirstNotificationService
{
    public function notifyFirstApprover(Creacion $c): void
    {
        $roles = ['comite_acreditacion'];

        $users = User::role($roles)->get();

        if ($users->isEmpty()) {
            Log::warning('No se encontraron usuarios para Comité de Acreditación', [
                'creacion_id' => $c->id,
                'roles'       => $roles,
            ]);
            return;
        }

        Log::info('Enviando notificaciones a Comité de Acreditación', [
            'creacion_id' => $c->id,
            'user_ids'    => $users->pluck('id')->all(),
        ]);

        Notification::send(
            $users,
            new CreacionDecisionNotification(
                $c,
                'comite',
                'pendiente',
                'creacion',
                null
            )
        );
    }
}
