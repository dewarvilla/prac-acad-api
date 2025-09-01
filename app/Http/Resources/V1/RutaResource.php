<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RutaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'origenLat' => $this->origen_lat,
            'origenLng' => $this->origen_lng,
            'destinoLat' => $this->destino_lat,
            'destinoLng' => $this->destino_lng,
            'numeroRecorridos' => $this->numero_recorridos,
            'numeroPeajes' => $this->numero_peajes,
            'valorPeajes' => $this->valor_peajes,
            'valorTotalPeajes' => $this->valor_total_peajes,
            'distanciaTrayectosKm' => $this->distancia_trayectos_km,
            'distanciaTotalKm' => $this->distancia_total_km,
            'rutaSalida' => $this->ruta_salida,
            'rutaLlegada' => $this->ruta_llegada,
            'practicaId' => $this->practica_id,
        ];
    }
}
