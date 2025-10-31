<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RoutesComputeService
{
    /**
     * Calcula distancia, duración y polyline usando Google Routes API.
     *
     * @param array $opts [
     *   'origin' => ['lat' => float, 'lng' => float],
     *   'dest'   => ['lat' => float, 'lng' => float],
     *   'mode'   => string ('DRIVE'|'WALK'|'BICYCLE'|'TWO_WHEELER')
     * ]
     * @return array|null ['distance_m'=>float, 'duration_s'=>float, 'polyline'=>string]
     */
    public function computeMetrics(array $opts): ?array
    {
        $origin = $opts['origin'] ?? null;
        $dest   = $opts['dest'] ?? null;
        $mode   = strtoupper($opts['mode'] ?? 'DRIVE');

        if (!$origin || !$dest) {
            throw new \InvalidArgumentException('Faltan coordenadas de origen o destino.');
        }

        $apiKey = config('services.google.api_key', env('GOOGLE_MAPS_API_KEY'));
        if (!$apiKey) {
            throw new \RuntimeException('No se configuró GOOGLE_MAPS_API_KEY en .env');
        }

        $url = 'https://routes.googleapis.com/directions/v2:computeRoutes';
        $payload = [
            'origin' => [
                'location' => [
                    'latLng' => [
                        'latitude'  => (float) $origin['lat'],
                        'longitude' => (float) $origin['lng'],
                    ],
                ],
            ],
            'destination' => [
                'location' => [
                    'latLng' => [
                        'latitude'  => (float) $dest['lat'],
                        'longitude' => (float) $dest['lng'],
                    ],
                ],
            ],
            'travelMode' => $mode,
            'routingPreference' => 'TRAFFIC_UNAWARE',
            'computeAlternativeRoutes' => false,
            'routeModifiers' => [
                'avoidTolls' => false,
                'avoidHighways' => false,
                'avoidFerries' => false,
            ],
            'languageCode' => 'es',
            'units' => 'METRIC',
        ];

        $headers = [
            'X-Goog-Api-Key'    => $apiKey,
            'X-Goog-FieldMask'  => 'routes.distanceMeters,routes.duration,routes.polyline.encodedPolyline',
            'Content-Type'      => 'application/json',
        ];

        try {
            $response = Http::withHeaders($headers)->post($url, $payload);

            if ($response->failed()) {
                Log::error('Google Routes API error: ' . $response->body());
                return null;
            }

            $json = $response->json();
            $route = $json['routes'][0] ?? null;
            if (!$route) {
                Log::warning('No se obtuvo ruta válida de Google Routes API.');
                return null;
            }

            return [
                'distance_m' => $route['distanceMeters'] ?? null,
                'duration_s' => isset($route['duration'])
                    ? $this->parseDuration($route['duration'])
                    : null,
                'polyline'   => $route['polyline']['encodedPolyline'] ?? null,
            ];
        } catch (\Throwable $e) {
            Log::error('Error en RoutesComputeService: ' . $e->getMessage());
            return null;
        }
    }

    private function parseDuration($v): ?float
    {
        if (is_string($v) && preg_match('/(\d+)s$/', $v, $m)) {
            return (float) $m[1];
        }
        return null;
    }
}
