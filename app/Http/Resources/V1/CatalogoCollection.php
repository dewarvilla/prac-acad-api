<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CatalogoCollection extends ResourceCollection
{
    public $collects = CatalogoResource::class;

    public function toArray(Request $request): array
    {
        return ['data' => $this->collection];
    }
}
