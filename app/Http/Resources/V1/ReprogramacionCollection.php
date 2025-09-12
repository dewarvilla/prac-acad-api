<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ReprogramacionCollection extends ResourceCollection
{
    public $collects = ReprogramacionResource::class;

    public function toArray(Request $request): array
    {
        return ['data' => $this->collection];
    }
}
