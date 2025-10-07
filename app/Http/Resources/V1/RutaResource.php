<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RutaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'programacionId'  => $this->programacion_id,

            'origen' => [
                'lat'     => $this->origen_lat,
                'lng'     => $this->origen_lng,
                'desc'    => $this->origen_desc,
                'placeId' => $this->origen_place_id,
            ],
            'destino' => [
                'lat'     => $this->destino_lat,
                'lng'     => $this->destino_lng,
                'desc'    => $this->destino_desc,
                'placeId' => $this->destino_place_id,
            ],

            'distanciaM'  => $this->distancia_m,
            'duracionS'   => $this->duracion_s,
            'polyline'    => $this->polyline,

            'numeroPeajes'=> $this->numero_peajes,
            'valorPeajes' => $this->valor_peajes,
            'orden'       => $this->orden,
            'justificacion'=> $this->justificacion,
            'estado'      => (bool) $this->estado,

            'fechacreacion'     => $this->fechacreacion?->toDateTimeString(),
            'fechamodificacion' => $this->fechamodificacion?->toDateTimeString(),
            'usuariocreacion'   => $this->usuariocreacion,
            'usuariomodificacion'=> $this->usuariomodificacion,
            'ipcreacion'        => $this->ipcreacion,
            'ipmodificacion'    => $this->ipmodificacion,
        ];
    }
}
