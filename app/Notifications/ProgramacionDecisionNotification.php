<?php

namespace App\Notifications;

use App\Models\Programacion;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProgramacionDecisionNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected Programacion $programacion,
        protected string $actorKey,    
        protected string $decision,   
        protected string $audience     
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'            => 'programacion_decision',
            'programacion_id' => $this->programacion->id,
            'nombre_practica' => $this->programacion->nombre_practica,
            'estado_practica' => $this->programacion->estado_practica,
            'actor'           => $this->actorKey,
            'decision'        => $this->decision,
            'audience'        => $this->audience,
            'title'           => $this->buildTitle(),
            'message'         => $this->buildMessage(),
        ];
    }

    protected function buildTitle(): string
    {
        if ($this->decision === 'aprobada') {
            return 'Programación aprobada';
        }
        return 'Programación rechazada';
    }

    protected function actorLabel(): string
    {
        return [
            'depart'     => 'Jefe de Departamento',
            'postg'      => 'Coordinador de Postgrados',
            'decano'     => 'Decano',
            'jefe_postg' => 'Jefe de Oficina de Postgrados',
            'vice'       => 'Vicerrectoría Académica',
        ][$this->actorKey] ?? $this->actorKey;
    }

    protected function buildMessage(): string
    {
        $actor = $this->actorLabel();
        $id    = $this->programacion->id;
        $name  = $this->programacion->nombre_practica;

        if ($this->audience === 'docente') {
            if ($this->decision === 'aprobada') {
                return "El {$actor} aprobó la programación #{$id} — «{$name}».";
            }
            return "El {$actor} rechazó la programación #{$id} — «{$name}».";
        }

        if ($this->decision === 'aprobada') {
            return "Tienes una programación #{$id} — «{$name}» pendiente de revisión después de la aprobación del {$actor}.";
        }

        return "Una programación #{$id} — «{$name}» fue rechazada por el {$actor}.";
    }
}
