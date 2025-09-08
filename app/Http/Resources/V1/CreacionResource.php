<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CreacionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                     => $this->id,
            'nivelAcademico'         => $this->nivel_academico,
            'facultad'               => $this->facultad,
            'programaAcademico'      => $this->programa_academico,
            'nombrePractica'         => $this->nombre_practica,
            'recursosNecesarios'     => $this->recursos_necesarios,
            'justificacion'           => $this->justificacion,
            'estadoPractica'         => $this->estado_practica,
            'estadoDepart'           => $this->estado_depart,
            'estadoConsejoFacultad'  => $this->estado_consejo_facultad,
            'estadoConsejoAcademico' => $this->estado_consejo_academico,
            'fechacreacion'          => $this->fechacreacion,
            'usuariocreacion'        => $this->usuariocreacion,
            'fechamodificacion'      => $this->fechamodificacion,
            'usuariomodificacion'    => $this->usuariomodificacion,
            'ipcreacion'             => $this->ipcreacion,
            'ipmodificacion'         => $this->ipmodificacion,
        ];
    }
}
