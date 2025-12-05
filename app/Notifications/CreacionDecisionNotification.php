<?php

namespace App\Notifications;

use App\Models\Creacion;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CreacionDecisionNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Creacion $creacion,
        public string $actorKey,        // 'comite_acreditacion', 'consejo_facultad', 'consejo_academico'
        public string $estado,          // 'aprobada', 'rechazada', 'pendiente'
        public string $tipo,            // 'creacion', 'siguiente', 'cambio_estado', 'docente_rechazo'
        public ?string $justificacion = null
    ) {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $c      = $this->creacion;
        $nombre = $c->nombre_practica ?? ('Creación #'.$c->id);

        $actorLabel = match ($this->actorKey) {
            'comite_acreditacion' => 'Comité de Acreditación y Currículo',
            'consejo_facultad'    => 'Consejo de Facultad',
            'consejo_academico'   => 'Consejo Académico',
            default               => 'Instancia',
        };

        if ($this->tipo === 'creacion') {
            $title   = 'Nueva solicitud de práctica académica';
            $message = "Se ha solicitado la creación de la práctica «{$nombre}» y "
                     . "requiere revisión del {$actorLabel}.";

        } elseif ($this->tipo === 'siguiente') {
            $title   = 'Solicitud lista para su revisión';
            $message = "La solicitud de creación «{$nombre}» ha avanzado en el flujo y "
                     . "ahora requiere revisión del {$actorLabel}.";

        } elseif ($this->tipo === 'docente_rechazo') {
            $title   = 'Tu solicitud de creación fue rechazada';
            $message = "Tu solicitud de creación «{$nombre}» ha sido rechazada por el {$actorLabel}. "
                     . "Por favor revisa la justificación.";

        } elseif ($this->tipo === 'cambio_estado') {
            if ($this->estado === 'aprobada') {
                $title   = 'Solicitud de creación aprobada';
                $message = "La solicitud de creación «{$nombre}» ha sido aprobada por el {$actorLabel} "
                         . "y la práctica académica ha quedado creada oficialmente.";
            } elseif ($this->estado === 'rechazada') {
                $title   = 'Solicitud de creación rechazada';
                $message = "La solicitud de creación «{$nombre}» ha sido rechazada por el {$actorLabel}.";
            } else {
                $title   = 'Actualización de solicitud de creación';
                $message = "La solicitud de creación «{$nombre}» ha cambiado de estado a «{$this->estado}».";
            }
        } else {
            $title   = 'Actualización de solicitud de creación';
            $message = "La solicitud de creación «{$nombre}» ha tenido un cambio, por favor revísala.";
        }

        return [
            'creacion_id'   => $c->id,
            'actor'         => $this->actorKey,
            'estado'        => $this->estado,
            'tipo'          => $this->tipo,
            'title'         => $title,
            'message'       => $message,
            'justificacion' => $this->justificacion,
        ];
    }
}
