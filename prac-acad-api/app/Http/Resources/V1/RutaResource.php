<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RutaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'latitudSalidas'       => $this->latitud_salidas,
            'latitudLlegadas'      => $this->latitud_llegadas,
            'numeroRecorridos'     => $this->numero_recorridos,
            'numeroPeajes'         => $this->numero_peajes,
            'valorPeajes'          => $this->valor_peajes,
            'distanciaTrayectosKm' => $this->distancia_trayectos_km,
            'rutaSalida'           => $this->ruta_salida,
            'rutaLlegada'          => $this->ruta_llegada,
            'programacionId'       => $this->programacion_id,

            // Auditoría
            'fechacreacion'        => $this->fechacreacion,
            'usuariocreacion'      => $this->usuariocreacion,
            'fechamodificacion'    => $this->fechamodificacion,
            'usuariomodificacion'  => $this->usuariomodificacion,
            'ipcreacion'           => $this->ipcreacion,
            'ipmodificacion'       => $this->ipmodificacion,
        ];
    }
}
