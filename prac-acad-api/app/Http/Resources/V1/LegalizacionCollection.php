<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class LegalizacionCollection extends ResourceCollection
{
    public $collects = LegalizacionResource::class;

    public function toArray(Request $request): array
    {
        return ['data' => $this->collection];
    }
}

