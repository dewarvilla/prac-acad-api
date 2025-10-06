<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProgramacionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'nombrePractica'             => $this->nombre_practica,
            'descripcion'        => $this->descripcion,
            'lugarDeRealizacion' => $this->lugar_de_realizacion,
            'justificacion'      => $this->justificacion,
            'recursosNecesarios' => $this->recursos_necesarios,
            'estadoPractica'     => $this->estado_practica,
            'estadoDepart'       => $this->estado_depart,
            'estadoPostg'        => $this->estado_postg,
            'estadoDecano'       => $this->estado_decano,
            'estadoJefePostg'    => $this->estado_jefe_postg,
            'estadoVice'         => $this->estado_vice,
            'fechaInicio'        => $this->fecha_inicio,
            'fechaFinalizacion'  => $this->fecha_finalizacion,
            'creacionId'         => $this->creacion_id,
            'fechacreacion'      => $this->fechacreacion,
            'usuariocreacion'    => $this->usuariocreacion,
            'fechamodificacion'  => $this->fechamodificacion,
            'usuariomodificacion'=> $this->usuariomodificacion,
            'ipcreacion'         => $this->ipcreacion,
            'ipmodificacion'     => $this->ipmodificacion,
            'requiereTransporte' => $this->requiere_transporte, 
            
        ];
    }
}
