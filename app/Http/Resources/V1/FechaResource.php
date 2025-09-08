<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FechaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'fechaAperturaPreg'          => $this->fecha_apertura_preg,
            'fechaCierreDocentePreg'     => $this->fecha_cierre_docente_preg,
            'fechaCierreJefeDepart'      => $this->fecha_cierre_jefe_depart,
            'fechaCierreDecano'          => $this->fecha_cierre_decano,
            'fechaAperturaPostg'         => $this->fecha_apertura_postg,
            'fechaCierreDocentePostg'    => $this->fecha_cierre_docente_postg,
            'fechaCierreCoordinadorPostg'=> $this->fecha_cierre_coordinador_postg,
            'fechacreacion'              => $this->fechacreacion,
            'usuariocreacion'            => $this->usuariocreacion,
            'fechamodificacion'          => $this->fechamodificacion,
            'usuariomodificacion'        => $this->usuariomodificacion,
            'ipcreacion'                 => $this->ipcreacion,
            'ipmodificacion'             => $this->ipmodificacion,
        ];
    }
}

