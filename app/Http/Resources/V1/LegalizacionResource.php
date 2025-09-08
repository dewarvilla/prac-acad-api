<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LegalizacionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'fechaLegalizacion'  => $this->fecha_legalizacion,
            'estadoDepart'       => $this->estado_depart,
            'estadoPostg'        => $this->estado_postg,
            'estadoLogistica'    => $this->estado_logistica,
            'estadoTesoreria'    => $this->estado_tesoreria,
            'estadoContabilidad' => $this->estado_contabilidad,
            'programacionId'     => $this->programacion_id,
            'fechacreacion'      => $this->fechacreacion,
            'usuariocreacion'    => $this->usuariocreacion,
            'fechamodificacion'  => $this->fechamodificacion,
            'usuariomodificacion'=> $this->usuariomodificacion,
            'ipcreacion'         => $this->ipcreacion,
            'ipmodificacion'     => $this->ipmodificacion,
        ];
    }
}
