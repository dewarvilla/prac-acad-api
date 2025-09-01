<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LegalizacionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'fechaLegalizacion' => $this->fecha_legalizacion,
            'estadoDepart' => $this->estado_depart,
            'estadoPostg' => $this->estado_postg,
            'estadoTesoreria' => $this->estado_tesoreria,
            'estadoContabilidad' => $this->estado_contabilidad,
            'practicaId' => $this->practica_id,
        ];
    }
}
