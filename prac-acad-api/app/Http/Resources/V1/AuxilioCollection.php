<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class AuxilioCollection extends ResourceCollection
{
    public $collects = AuxilioResource::class;

    public function toArray(Request $request): array
    {
        return ['data' => $this->collection];
    }
}
