<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParticipanteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'numeroIdentificacion' => $this->numero_identificacion,
            'tipoParticipante'     => $this->tipo_participante, // estudiante|docente|acompanante
            'discapacidad'         => $this->discapacidad,
            'nombre'               => $this->nombre,
            'correoInstitucional'  => $this->correo_institucional,
            'telefono'             => $this->telefono,
            'programaAcademico'    => $this->programa_academico,
            'facultad'             => $this->facultad,
            'repitente'            => $this->repitente,
            'programacionId'       => $this->programacion_id,
            'fechacreacion'        => $this->fechacreacion,
            'usuariocreacion'      => $this->usuariocreacion,
            'fechamodificacion'    => $this->fechamodificacion,
            'usuariomodificacion'  => $this->usuariomodificacion,
            'ipcreacion'           => $this->ipcreacion,
            'ipmodificacion'       => $this->ipmodificacion,
        ];
    }
}
