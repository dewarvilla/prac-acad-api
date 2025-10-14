<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Ruta;
use App\Models\Rutapeaje;

class RutapeajesSyncService
{
    public function syncFromSocrata(Ruta $ruta, float $bufferDeg = 0.05, int $thresholdM = 120): int
    {
        if (!$ruta->polyline) return 0;

        $poly = $this->decodePolyline($ruta->polyline);
        [$minLat,$minLon,$maxLat,$maxLon] = $this->bbox($poly, $bufferDeg);

        $headers = [];
        if ($token = env('SODA_APP_TOKEN')) $headers['X-App-Token'] = $token;

        $q = [
            '%24where' => "within_box(point,$maxLat,$minLon,$minLat,$maxLon)",
            '%24limit' => 5000
        ];
        $res = Http::withHeaders($headers)
            ->get('https://www.datos.gov.co/resource/68qj-5xux.json', $q)
            ->json();

        // Borra peajes previos de esta ruta (rebuild)
        $ruta->peajes()->delete();

        $inserted = 0;
        foreach ($res as $p) {
            $coords = $p['point']['coordinates'] ?? null; // [lon,lat]
            if (!$coords) continue;
            [$plon,$plat] = $coords;

            $dist = $this->minDistancePointToPathMeters([$plat,$plon], $poly);
            if ($dist > $thresholdM) continue;

            Rutapeaje::create([
                'ruta_id'  => $ruta->id,
                'nombre'   => $p['nombre_peaje'] ?? ($p['peaje'] ?? 'Peaje'),
                'lat'      => $plat,
                'lng'      => $plon,
                'distancia_m' => (int) round($dist),
                'orden_km' => null, // puedes calcularlo si proyectas el punto sobre la polilínea
                'cat_i'    => self::num($p['categoria_i']   ?? null),
                'cat_ii'   => self::num($p['categoria_ii']  ?? null),
                'cat_iii'  => self::num($p['categoria_iii'] ?? null),
                'cat_iv'   => self::num($p['categoria_iv']  ?? null),
                'cat_v'    => self::num($p['categoria_v']   ?? null),
                'cat_vi'   => self::num($p['categoria_vi']  ?? null),
                'cat_vii'  => self::num($p['categoria_vii'] ?? null),
                'fuente'   => 'datos.gov.co:68qj-5xux',
                'fecha_tarifa' => now()->toDateString(),
                'fechacreacion' => now(),
                'fechamodificacion' => now(),
            ]);
            $inserted++;
        }

        // Actualiza totales cacheados
        $ruta->update([
            'numero_peajes' => $ruta->peajes()->count(),
            'valor_peajes'  => null, // lo puedes setear si envías una cat específica
            'fechamodificacion' => now(),
        ]);

        return $inserted;
    }

    // ===== utilidades =====
    private static function num($v) {
        if ($v === null) return null;
        return (float) preg_replace('/[^\d\.]/','',(string)$v);
    }

    private function decodePolyline($encoded)
    {
        $points = [];
        $index = $lat = $lng = 0; $len = strlen($encoded);
        while ($index < $len) {
            $b = $shift = $result = 0;
            do { $b = ord($encoded[$index++]) - 63; $result |= ($b & 0x1f) << $shift; $shift += 5; } while ($b >= 0x20);
            $dlat = (($result & 1) ? ~($result >> 1) : ($result >> 1)); $lat += $dlat;
            $shift = $result = 0;
            do { $b = ord($encoded[$index++]) - 63; $result |= ($b & 0x1f) << $shift; $shift += 5; } while ($b >= 0x20);
            $dlng = (($result & 1) ? ~($result >> 1) : ($result >> 1)); $lng += $dlng;
            $points[] = [$lat * 1e-5, $lng * 1e-5];
        }
        return $points;
    }

    private function bbox(array $poly,float $m=0.02)
    {
        $minLat=$minLon= 999; $maxLat=$maxLon=-999;
        foreach ($poly as [$la,$lo]) {
            $minLat = min($minLat,$la); $maxLat = max($maxLat,$la);
            $minLon = min($minLon,$lo); $maxLon = max($maxLon,$lo);
        }
        return [$minLat-$m,$minLon-$m,$maxLat+$m,$maxLon+$m];
    }

    private function minDistancePointToPathMeters(array $p,array $path)
    {
        $min=INF;
        for($i=0;$i<count($path)-1;$i++){
            $d=$this->pointToSegmentDistanceMeters($p,$path[$i],$path[$i+1]);
            if($d<$min)$min=$d;
        }
        return $min;
    }

    private function pointToSegmentDistanceMeters($p,$a,$b)
    {
        [$plat,$plon]=$p; [$alat,$alon]=$a; [$blat,$blon]=$b;
        $ax=$alon;$ay=$alat;$bx=$blon;$by=$blat;$px=$plon;$py=$plat;
        $dx=$bx-$ax;$dy=$by-$ay;
        if($dx==0 && $dy==0) return $this->hav($plat,$plon,$alat,$alon);
        $t=(($px-$ax)*$dx+($py-$ay)*$dy)/($dx*$dx+$dy*$dy);
        $t=max(0,min(1,$t));
        $lon=$ax+$t*$dx; $lat=$ay+$t*$dy;
        return $this->hav($plat,$plon,$lat,$lon);
    }

    private function hav($lat1,$lon1,$lat2,$lon2)
    {
        $R=6371000;
        $dLat=deg2rad($lat2-$lat1); $dLon=deg2rad($lon2-$lon1);
        $a=sin($dLat/2)**2 + cos(deg2rad($lat1))*cos(deg2rad($lat2))*sin($dLon/2)**2;
        return 2*$R*asin(min(1,sqrt($a)));
    }
}