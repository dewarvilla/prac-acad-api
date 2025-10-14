<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class RutapeajeCollection extends ResourceCollection
{
    public $collects = RutapeajeResource::class;
    public function toArray($request): array
    {
        return ['data' => $this->collection];
    }
}