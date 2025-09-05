<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AjusteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'ajusteId'                  => $this->ajuste_id,
            'fechaAjuste'               => $this->fecha_ajuste,
            'estadoAjuste'              => $this->estado_ajuste, 
            'estadoVice'                => $this->estado_vice,          
            'estadoJefeDepart'          => $this->estado_jefe_depart,         
            'estadoCoordinadorPostg'    => $this->estado_coordinador_postg,          
            'justificacion'             => $this->justificacion,
            'programacionId'           => $this->programacion_id,
            'fechacreacion'             => $this->fechacreacion,
            'usuariocreacion'           => $this->usuariocreacion,
            'fechamodificacion'         => $this->fechamodificacion,
            'usuariomodificacion'       => $this->usuariomodificacion,
            'ipcreacion'                => $this->ipcreacion,
            'ipmodificacion'            => $this->ipmodificacion,
        ];
    }
}
