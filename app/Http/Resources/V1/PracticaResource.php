<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PracticaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'nombre' => $this->nombre,
            'nivel' => $this->nivel,
            'facultad' => $this->facultad,
            'programaAcademico' => $this->programa_academico,
            'descripcion' => $this->descripcion,
            'lugarDeRealizacion' => $this->lugar_de_realizacion,
            'justificacion' => $this->justificacion,
            'recursosNecesarios' => $this->recursos_necesarios,
            'estadoPractica' => $this->estado_practica,
            'estadoDepart' => $this->estado_depart,
            'estadoPostg' => $this->estado_postg,
            'estadoDecano' => $this->estado_decano,
            'estadoJefePostg' => $this->estado_jefe_postg,
            'estadoVice' => $this->estado_vice,
            'fechaFinalizacion' => $this->fecha_finalizacion,
            'fechaSolicitud' => $this->fecha_solicitud,
            'userId' => $this->user_id, 
        ];
    }
}
