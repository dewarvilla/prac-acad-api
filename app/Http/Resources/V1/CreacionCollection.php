<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CreacionCollection extends ResourceCollection
{
    public $collects = CreacionResource::class;

    public function toArray(Request $request): array
    {
        return ['data' => $this->collection];
    }
}
