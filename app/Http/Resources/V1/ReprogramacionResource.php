<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReprogramacionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'fechaReprogramacion' => $this->fecha_reprogramacion,
            'estadoReprogramacion' => $this->estado_reprogramacion,
            'tipoReprogramacion' => $this->tipo_reprogramacion,
            'estadoVice' => $this->estado_vice,
            'justificacion' => $this->justificacion,
            'practicaId' => $this->practica_id,
        ];
    }
}
