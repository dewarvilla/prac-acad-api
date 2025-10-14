<?php

namespace App\Http\Controllers;

use App\Services\RouteUsageLimiter;

class UsageController extends Controller
{
    public function preflight(RouteUsageLimiter $limiter)
    {
        $res = $limiter->preflight();
        return response()->json($res);
    }

    public function stats(RouteUsageLimiter $limiter)
    {
        $res = $limiter->stats();
        return response()->json(['stats' => $res]);
    }
}
