<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Ruta;
use App\Models\Rutapeaje;

class RutapeajesSyncService
{
    /**
     * Sincroniza los peajes cercanos a una ruta usando el dataset oficial de Datos Abiertos Colombia.
     *
     * @param  Ruta  $ruta
     * @param  string|null $categoriaVehiculo  Categoría (I–VII)
     * @param  float $bufferDeg  Margen de búsqueda (en grados)
     * @param  int   $thresholdM Distancia máxima (en metros) para considerar un peaje cercano
     * @return array { insertados, total_valor }
     */
    public function syncFromSocrata(Ruta $ruta, ?string $categoriaVehiculo = null, float $bufferDeg = 0.25, int $thresholdM = 500): array
    {
        if (!$ruta->polyline || strlen($ruta->polyline) < 10) {
            return ['insertados' => 0, 'total_valor' => 0];
        }
        $cat = strtoupper(trim($categoriaVehiculo ?? $ruta->categoria_vehiculo ?? 'I'));

        $poly = $this->decodePolyline($ruta->polyline);
        [$minLat, $minLon, $maxLat, $maxLon] = $this->bbox($poly, $bufferDeg);

        $headers = [];
        if ($token = env('SODA_APP_TOKEN')) {
            $headers['X-App-Token'] = $token;
        }

        $url = 'https://www.datos.gov.co/api/v3/views/68qj-5xux/query.json';
        $query = [
            'where' => "within_box(point,$maxLat,$minLon,$minLat,$maxLon)",
            'limit' => 5000
        ];

        $response = Http::withHeaders($headers)->get($url, $query);

        if ($response->failed()) {
            throw new \Exception('Error al consultar peajes desde Datos Abiertos: ' . $response->body());
        }

        $data = $response->json();
        $ruta->peajes()->delete();

        $inserted = 0;
        $totalValor = 0;
        $peajesEncontrados = [];
        foreach ($data as $p) {
            $coords = $p['point']['coordinates'] ?? null;
            if (!$coords) continue;

            [$plon, $plat] = $coords;
            $dist = $this->minDistancePointToPathMeters([$plat, $plon], $poly);
            if ($dist > $thresholdM) continue;

            $nombrePeaje = trim(strtoupper($p['nombre_peaje'] ?? ($p['peaje'] ?? 'PEAJE')));
            $nombreNormalizado = preg_replace('/\s*\d+$/', '', $nombrePeaje); 
            if (isset($peajesEncontrados[$nombreNormalizado])) {
                continue;
            }

            $catValues = [
                'I'   => self::num($p['categoria_i'] ?? null),
                'II'  => self::num($p['categoria_ii'] ?? null),
                'III' => self::num($p['categoria_iii'] ?? null),
                'IV'  => self::num($p['categoria_iv'] ?? null),
                'V'   => self::num($p['categoria_v'] ?? null),
                'VI'  => self::num($p['categoria_vi'] ?? null),
                'VII' => self::num($p['categoria_vii'] ?? null),
            ];

            $valorCat = $catValues[$cat] ?? null;
            if ($valorCat !== null) $totalValor += $valorCat;

            Rutapeaje::create([
                'ruta_id'            => $ruta->id,
                'nombre'             => $nombrePeaje,
                'lat'                => $plat,
                'lng'                => $plon,
                'distancia_m'        => (int) round($dist),
                'orden_km'           => null,
                'categoria_vehiculo' => $cat,
                'cat_i'              => $catValues['I'],
                'cat_ii'             => $catValues['II'],
                'cat_iii'            => $catValues['III'],
                'cat_iv'             => $catValues['IV'],
                'cat_v'              => $catValues['V'],
                'cat_vi'             => $catValues['VI'],
                'cat_vii'            => $catValues['VII'],
                'valor_total'        => $valorCat,
                'fuente'             => 'datos.gov.co:68qj-5xux',
                'fecha_tarifa'       => now()->toDateString(),
                'fechacreacion'      => now(),
                'fechamodificacion'  => now(),
            ]);

            $peajesEncontrados[$nombreNormalizado] = true;
            $inserted++;
        }

        $ruta->update([
            'categoria_vehiculo' => $cat,    
            'numero_peajes'      => $inserted,
            'valor_peajes'       => $totalValor,
            'fechamodificacion'  => now(),
        ]);

        return ['insertados' => $inserted, 'total_valor' => $totalValor];
    }

    /* === helpers === */
    private static function num($v): ?float
    {
        if (is_null($v) || $v === '' || $v === '-') return null;
        return is_numeric($v) ? (float) $v : null;
    }

    private function bbox(array $poly, float $buf): array
    {
        $lats = array_column($poly, 'lat');
        $lngs = array_column($poly, 'lng');
        return [
            min($lats) - $buf,
            min($lngs) - $buf,
            max($lats) + $buf,
            max($lngs) + $buf
        ];
    }

    private function decodePolyline(string $str): array
    {
        $index = 0;
        $lat = 0;
        $lng = 0;
        $coords = [];
        while ($index < strlen($str)) {
            $b = 0;
            $shift = 0;
            $result = 0;
            do {
                $b = ord($str[$index++]) - 63;
                $result |= ($b & 0x1f) << $shift;
                $shift += 5;
            } while ($b >= 0x20);
            $dlat = ($result & 1) ? ~($result >> 1) : ($result >> 1);
            $lat += $dlat;

            $shift = 0;
            $result = 0;
            do {
                $b = ord($str[$index++]) - 63;
                $result |= ($b & 0x1f) << $shift;
                $shift += 5;
            } while ($b >= 0x20);
            $dlng = ($result & 1) ? ~($result >> 1) : ($result >> 1);
            $lng += $dlng;

            $coords[] = ['lat' => $lat / 1e5, 'lng' => $lng / 1e5];
        }
        return $coords;
    }

    private function minDistancePointToPathMeters(array $p, array $path): float
    {
        $min = INF;
        for ($i = 1; $i < count($path); $i++) {
            $min = min($min, $this->distancePointToSegmentMeters($p, $path[$i - 1], $path[$i]));
        }
        return $min;
    }

    private function distancePointToSegmentMeters(array $p, array $a, array $b): float
    {
        $toRad = fn($deg) => $deg * M_PI / 180;
        $R = 6371000; // radio tierra (m)
        $lat1 = $toRad($a['lat']);
        $lng1 = $toRad($a['lng']);
        $lat2 = $toRad($b['lat']);
        $lng2 = $toRad($b['lng']);
        $lat3 = $toRad($p[0]);
        $lng3 = $toRad($p[1]);
        $A = [$lat1, $lng1];
        $B = [$lat2, $lng2];
        $P = [$lat3, $lng3];
        $dx = $B[1] - $A[1];
        $dy = $B[0] - $A[0];
        $t = (($P[1] - $A[1]) * $dx + ($P[0] - $A[0]) * $dy) / ($dx * $dx + $dy * $dy);
        $t = max(0, min(1, $t));
        $proj = [$A[0] + $t * $dy, $A[1] + $t * $dx];
        return $R * sqrt(pow($proj[0] - $P[0], 2) + pow($proj[1] - $P[1], 2));
    }
}
