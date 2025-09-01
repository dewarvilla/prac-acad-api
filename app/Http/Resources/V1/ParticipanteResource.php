<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParticipanteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'numeroIdentificacion' => $this->numero_identificacion,
            'tipoParticipante' => $this->tipo_participante,
            'discapacidad' => $this->discapacidad,
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'correoInstitucional' => $this->correo_institucional,
            'telefono' => $this->telefono,
            'programaAcademico' => $this->programa_academico,
            'facultad' => $this->facultad,
            'repitente' => $this->repitente,
            'practicaId' => $this->practica_id,
            'userId' => $this->user_id,
        ];
    }
}
