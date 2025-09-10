<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SalarioResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'anio'           => $this->anio,
            'valor'          => $this->valor,

            // Auditoría
            'fechacreacion'      => $this->fechacreacion,
            'usuariocreacion'    => $this->usuariocreacion,
            'fechamodificacion'  => $this->fechamodificacion,
            'usuariomodificacion'=> $this->usuariomodificacion,
            'ipcreacion'         => $this->ipcreacion,
            'ipmodificacion'     => $this->ipmodificacion,
        ];
    }
}
