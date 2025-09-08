<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReprogramacionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'fechaReprogramacion'  => $this->fecha_reprogramacion,
            'estadoReprogramacion' => $this->estado_reprogramacion,
            'tipoReprogramacion'   => $this->tipo_reprogramacion,
            'estadoVice'           => $this->estado_vice,
            'justificacion'        => $this->justificacion,
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
