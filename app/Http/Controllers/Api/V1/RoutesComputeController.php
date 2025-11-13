<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\ComputeRouteRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RoutesComputeController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:rutas.view')->only(['compute']);
    }

    public function compute(ComputeRouteRequest $request)
    {
        $key = config('services.google_routes.key');

        if (!$key) {
            return response()->json([
                'distance_m' => null,
                'duration_s' => null,
                'polyline'   => null,
                'warning'    => 'GOOGLE_ROUTES_API_KEY no configurada',
            ]);
        }

        $origin = $request->validated('origin');
        $dest   = $request->validated('dest');
        $mode   = $request->validated('mode') ?? 'DRIVE';

        $payload = [
            'origin' => ['location' => ['latLng' => ['latitude'=>(float)$origin['lat'], 'longitude'=>(float)$origin['lng']]]],
            'destination' => ['location' => ['latLng' => ['latitude'=>(float)$dest['lat'], 'longitude'=>(float)$dest['lng']]]],
            'travelMode'               => $mode,
            'computeAlternativeRoutes' => false,
            'routingPreference'        => config('services.google_routes.traffic_aware') ? 'TRAFFIC_AWARE' : 'TRAFFIC_UNAWARE',
            'polylineEncoding'         => 'ENCODED_POLYLINE',
            'units'                    => 'METRIC',
        ];

        try {
            $res = Http::withHeaders([
                    'X-Goog-Api-Key'   => $key,
                    'X-Goog-FieldMask' => 'routes.distanceMeters,routes.duration,routes.polyline.encodedPolyline',
                ])
                ->timeout(15)
                ->post('https://routes.googleapis.com/directions/v2:computeRoutes', $payload);

            if ($res->failed()) {
                Log::warning('Routes API failed', ['status'=>$res->status(),'body'=>$res->json()]);
                return response()->json([
                    'distance_m' => null,
                    'duration_s' => null,
                    'polyline'   => null,
                    'error'      => $res->json(),
                ]);
            }

            $route    = $res->json('routes.0');
            $distance = (int) ($route['distanceMeters'] ?? 0);
            $duration = isset($route['duration']) ? (int) preg_replace('/\D/', '', (string) $route['duration']) : null;
            $polyline = $route['polyline']['encodedPolyline'] ?? null;

            return response()->json([
                'distance_m' => $distance ?: null,
                'duration_s' => $duration,
                'polyline'   => $polyline,
            ]);
        } catch (\Throwable $e) {
            Log::error('Routes API exception', ['msg'=>$e->getMessage()]);
            return response()->json([
                'distance_m' => null,
                'duration_s' => null,
                'polyline'   => null,
                'exception'  => $e->getMessage(),
            ]);
        }
    }
}
