<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CatalogoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                     => $this->id,
            'nivelAcademico'         => $this->nivel_academico,
            'facultad'               => $this->facultad,
            'programaAcademico'      => $this->programa_academico,
            'fechacreacion'          => $this->fechacreacion,
            'usuariocreacion'        => $this->usuariocreacion,
            'fechamodificacion'      => $this->fechamodificacion,
            'usuariomodificacion'    => $this->usuariomodificacion,
            'ipcreacion'             => $this->ipcreacion,
            'ipmodificacion'         => $this->ipmodificacion,
        ];
    }
}
