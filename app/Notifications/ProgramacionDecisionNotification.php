<?php

namespace App\Notifications;

use App\Models\Programacion;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProgramacionDecisionNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Programacion $programacion,
        public string $actorKey,  // 'depart', 'postg', 'decano', 'jefe_postg', 'vice'
        public string $estado,    // 'aprobada', 'rechazada', 'en_aprobacion'.
        public string $tipo,       // 'creacion', 'siguiente', 'cambio_estado', 'docente', etc.
        public ?string $justificacion = null
    ) {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $p      = $this->programacion;
        $nombre = $p->nombre_practica ?? ('Práctica #'.$p->id);

        $actorLabel = match ($this->actorKey) {
            'depart'     => 'Jefe de Departamento',
            'postg'      => 'Coordinador de Postgrados',
            'decano'     => 'Decano de Facultad',
            'jefe_postg' => 'Jefe de Postgrados',
            'vice'       => 'Vicerrectoría Académica',
            default      => 'Aprobador',
        };

        if ($this->tipo === 'creacion') {
            $title   = 'Nueva práctica para revisión';
            $message = "Se ha creado la práctica «{$nombre}» y requiere revisión del {$actorLabel}.";

        } elseif ($this->tipo === 'siguiente') {
            $title   = 'Práctica lista para su revisión';
            $message = "La práctica «{$nombre}» ha avanzado en el flujo y ahora requiere revisión del {$actorLabel}.";

        } elseif ($this->tipo === 'docente_rechazo') {
            $title   = 'Tu práctica fue rechazada';
            $message = "Tu práctica «{$nombre}» ha sido rechazada por el {$actorLabel}. "
                    . "Por favor revisa la justificación en el sistema.";

        } elseif ($this->tipo === 'cambio_estado') {
            if ($this->estado === 'aprobada') {
                $title   = 'Práctica aprobada';
                $message = "La práctica «{$nombre}» ha sido aprobada por el {$actorLabel}.";
            } elseif ($this->estado === 'rechazada') {
                $title   = 'Práctica rechazada';
                $message = "La práctica «{$nombre}» ha sido rechazada por el {$actorLabel}.";
            } else {
                $title   = 'Actualización de práctica';
                $message = "La práctica «{$nombre}» ha cambiado de estado a «{$this->estado}».";
            }
        } else {
            $title   = 'Actualización de práctica';
            $message = "La práctica «{$nombre}» ha tenido un cambio por favor volver a revisar.";
        }

        return [
            'programacion_id' => $p->id,
            'actor'           => $this->actorKey,
            'estado'          => $this->estado,
            'tipo'            => $this->tipo,
            'title'           => $title,
            'message'         => $message,
            'justificacion'   => $this->justificacion,
        ];
    }
}
