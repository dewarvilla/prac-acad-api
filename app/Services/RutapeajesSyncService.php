<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Ruta;
use App\Models\Rutapeaje;

class RutapeajesSyncService
{
    /**
     * Sincroniza los peajes cercanos a una ruta usando el dataset de INVÍAS
     * (Datos Abiertos Colombia: 68qj-5xux).
     *
     * @param  Ruta        $ruta
     * @param  string|null $categoriaVehiculo  Categoría (I–VII)
     * @param  float       $bufferDeg          Margen de búsqueda (en grados)
     * @param  int         $thresholdM         Distancia máxima (m) a la ruta
     * @return array { insertados, total_valor }
     */
    public function syncFromSocrata(
        Ruta $ruta,
        ?string $categoriaVehiculo = null,
        float $bufferDeg = 0.25,
        int $thresholdM = 3000
    ): array {
        if (!$ruta->polyline || strlen($ruta->polyline) < 10) {
            return ['insertados' => 0, 'total_valor' => 0];
        }

        $cat  = strtoupper(trim($categoriaVehiculo ?? $ruta->categoria_vehiculo ?? 'I'));

        $poly = $this->decodePolyline($ruta->polyline);
        [$minLat, $minLon, $maxLat, $maxLon] = $this->bbox($poly, $bufferDeg);

        $headers = [];
        if ($token = env('SODA_APP_TOKEN')) {
            $headers['X-App-Token'] = $token;
        }

        $urlInvias = 'https://www.datos.gov.co/api/v3/views/68qj-5xux/query.json';
        $queryInvias = [
            'where' => "within_box(point,$maxLat,$minLon,$minLat,$maxLon)",
            'limit' => 5000,
        ];

        $response = Http::withHeaders($headers)->get($urlInvias, $queryInvias);

        if ($response->failed()) {
            \Log::error('RutapeajesSyncService - Error consultando INVÍAS', [
                'ruta_id' => $ruta->id,
                'status'  => $response->status(),
                'body'    => $response->body(),
                'query'   => $queryInvias,
            ]);

            throw new \Exception('Error al consultar peajes desde Datos Abiertos (Invías).');
        }

        $dataInvias = $response->json();

        if (!is_array($dataInvias)) {
            \Log::warning('RutapeajesSyncService - Respuesta inesperada de INVÍAS', [
                'ruta_id' => $ruta->id,
                'body'    => $response->body(),
            ]);

            return ['insertados' => 0, 'total_valor' => 0];
        }

        $records = [];
        foreach ($dataInvias as $p) {
            $coords = $p['point']['coordinates'] ?? null;
            if (!$coords) {
                continue;
            }

            [$plon, $plat] = $coords;

            $nombrePeaje = trim(strtoupper($p['nombre_peaje'] ?? ($p['peaje'] ?? 'PEAJE')));

            $records[] = [
                'nombre'       => $nombrePeaje,
                'lat'          => $plat,
                'lng'          => $plon,
                'cat_i'        => self::num($p['categoria_i']   ?? null),
                'cat_ii'       => self::num($p['categoria_ii']  ?? null),
                'cat_iii'      => self::num($p['categoria_iii'] ?? null),
                'cat_iv'       => self::num($p['categoria_iv']  ?? null),
                'cat_v'        => self::num($p['categoria_v']   ?? null),
                'cat_vi'       => self::num($p['categoria_vi']  ?? null),
                'cat_vii'      => self::num($p['categoria_vii'] ?? null),
                'fuente'       => 'datos.gov.co:68qj-5xux',
                'fecha_tarifa' => null,
            ];
        }

        $ruta->peajes()->delete();

        $inserted          = 0;
        $totalValor        = 0;
        $peajesEncontrados = [];

        foreach ($records as $p) {
            $dist = $this->minDistancePointToPathMeters([$p['lat'], $p['lng']], $poly);

            if ($dist > $thresholdM) {
                continue;
            }

            $nombrePeaje       = $p['nombre'];
            $nombreNormalizado = $this->normalizeName($nombrePeaje);

            if (isset($peajesEncontrados[$nombreNormalizado])) {
                continue;
            }

            $catValues = [
                'I'   => $p['cat_i'],
                'II'  => $p['cat_ii'],
                'III' => $p['cat_iii'],
                'IV'  => $p['cat_iv'],
                'V'   => $p['cat_v'],
                'VI'  => $p['cat_vi'],
                'VII' => $p['cat_vii'],
            ];

            $valorCat = $catValues[$cat] ?? null;
            if ($valorCat !== null) {
                $totalValor += $valorCat;
            }

            Rutapeaje::create([
                'ruta_id'            => $ruta->id,
                'nombre'             => $nombrePeaje,
                'lat'                => $p['lat'],
                'lng'                => $p['lng'],
                'distancia_m'        => (int) round($dist),
                'orden_km'           => null,
                'categoria_vehiculo' => $cat,
                'cat_i'              => $p['cat_i'],
                'cat_ii'             => $p['cat_ii'],
                'cat_iii'            => $p['cat_iii'],
                'cat_iv'             => $p['cat_iv'],
                'cat_v'              => $p['cat_v'],
                'cat_vi'             => $p['cat_vi'],
                'cat_vii'            => $p['cat_vii'],
                'valor_total'        => $valorCat,
                'fuente'             => $p['fuente'],
                'fecha_tarifa'       => $p['fecha_tarifa'] ?? now()->toDateString(),
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

        // Log de resumen limpio
        \Log::info("RutapeajesSyncService - Ruta {$ruta->id} — Peajes sincronizados automáticamente", [
            'insertados'  => $inserted,
            'total_valor' => $totalValor,
            'categoria'   => $cat,
            'threshold_m' => $thresholdM,
            'buffer_deg'  => $bufferDeg,
        ]);

        return ['insertados' => $inserted, 'total_valor' => $totalValor];
    }

    private function normalizeName(string $name): string
    {
        $name = mb_strtoupper(trim($name), 'UTF-8');
        $name = preg_replace('/\s*\d+$/', '', $name);
        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT', $name);
        if ($ascii !== false) {
            $name = $ascii;
        }
        return preg_replace('/\s+/', ' ', $name);
    }

    private static function num($v): ?float
    {
        if (is_null($v)) return null;
        $v = trim((string)$v);
        if ($v === '' || $v === '-') return null;
        $clean = str_replace([',', ' '], '', $v);

        return is_numeric($clean) ? (float) $clean : null;
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
        $index  = 0;
        $lat    = 0;
        $lng    = 0;
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
        $R = 6371000; // radio de la Tierra en m

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

        return $R * sqrt(
            pow($proj[0] - $P[0], 2) +
            pow($proj[1] - $P[1], 2)
        );
    }
}
