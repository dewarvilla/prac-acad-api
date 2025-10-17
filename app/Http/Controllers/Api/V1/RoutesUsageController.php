<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\UsageCounter;

class RoutesUsageController extends Controller
{
    public function __construct(protected UsageCounter $usage) {}

    public function stats()
    {
        return response()->json($this->usage->stats());
    }

    public function preflight()
    {
        // Intenta reservar 1. Si no hay cupo, responde allowed:false
        $res = $this->usage->reserve();
        return response()->json($res);
    }
}
