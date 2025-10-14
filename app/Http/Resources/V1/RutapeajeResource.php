<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class RutapeajeResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'       => $this->id,
            'rutaId'   => $this->ruta_id,
            'nombre'   => $this->nombre,
            'lat'      => $this->lat,
            'lng'      => $this->lng,
            'distanciaM' => $this->distancia_m,
            'ordenKm'  => $this->orden_km,
            'tarifas'  => [
                'I'   => $this->cat_i,
                'II'  => $this->cat_ii,
                'III' => $this->cat_iii,
                'IV'  => $this->cat_iv,
                'V'   => $this->cat_v,
                'VI'  => $this->cat_vi,
                'VII' => $this->cat_vii,
            ],
            'fuente'   => $this->fuente,
            'fechaTarifa' => optional($this->fecha_tarifa)->toDateString(),
            'fechacreacion'     => optional($this->fechacreacion)->toDateTimeString(),
            'fechamodificacion' => optional($this->fechamodificacion)->toDateTimeString(),
        ];
    }
}