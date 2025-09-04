<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class FechaCollection extends ResourceCollection
{
    public $collects = FechaResource::class;

    public function toArray(Request $request): array
    {
        return ['data' => $this->collection];
    }

    public function with(Request $request): array
    {
        if (method_exists($this->resource, 'perPage')) {
            return [
                'meta' => [
                    'per_page'     => $this->resource->perPage(),
                    'current_page' => $this->resource->currentPage(),
                    'last_page'    => $this->resource->lastPage(),
                    'total'        => $this->resource->total(),
                ],
                'links' => [
                    'first' => $this->resource->url(1),
                    'last'  => $this->resource->url($this->resource->lastPage()),
                    'prev'  => $this->resource->previousPageUrl(),
                    'next'  => $this->resource->nextPageUrl(),
                ],
            ];
        }
        return [];
    }
}
