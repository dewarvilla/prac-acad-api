<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SalarioCollection extends ResourceCollection
{
    public $collects = SalarioResource::class;

    public function toArray(Request $request): array
    {
        return ['data' => $this->collection];
    }
}
