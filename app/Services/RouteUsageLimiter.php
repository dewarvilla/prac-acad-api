<?php

namespace App\Services;

use App\Models\RouteUsage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RouteUsageLimiter
{
    public function preflight(): array
    {
        $limit     = (int) config('services.route_usage.limit', (int) env('ROUTES_MONTHLY_LIMIT', 9750));
        $warnRatio = (float) config('services.route_usage.warn_ratio', (float) env('ROUTES_WARN_RATIO', 0.8));
        $monthKey  = Carbon::now()->format('Y-m');

        return DB::transaction(function () use ($monthKey, $limit, $warnRatio) {
            // Bloqueo de fila para operación atómica
            $usage = RouteUsage::where('month_key', $monthKey)->lockForUpdate()->first();

            if (!$usage) {
                $usage = RouteUsage::create([
                    'month_key'  => $monthKey,
                    'count'      => 0,
                    'limit'      => $limit,
                    'warn_ratio' => $warnRatio,
                ]);
            }

            // Asegura que el límite/umbral refleje el .env si cambiaste valores a mitad de mes
            $currentLimit     = $usage->limit ?: $limit;
            $currentWarnRatio = $usage->warn_ratio ?: $warnRatio;

            if ($usage->count >= $currentLimit) {
                return [
                    'allowed' => false,
                    'stats'   => [
                        'month'     => $usage->month_key,
                        'count'     => $usage->count,
                        'limit'     => $currentLimit,
                        'remaining' => 0,
                        'warn_ratio'=> $currentWarnRatio,
                        'warn_at'   => (int) floor($currentLimit * $currentWarnRatio),
                    ],
                ];
            }

            // Reserva 1
            $usage->increment('count');

            $usage->refresh();

            return [
                'allowed' => true,
                'stats'   => [
                    'month'     => $usage->month_key,
                    'count'     => $usage->count,
                    'limit'     => $currentLimit,
                    'remaining' => max(0, $currentLimit - $usage->count),
                    'warn_ratio'=> $currentWarnRatio,
                    'warn_at'   => (int) floor($currentLimit * $currentWarnRatio),
                ],
            ];
        }, 3); // 3 reintentos si hay contención
    }

    public function stats(): array
    {
        $limit     = (int) config('services.route_usage.limit', (int) env('ROUTES_MONTHLY_LIMIT', 9750));
        $warnRatio = (float) config('services.route_usage.warn_ratio', (float) env('ROUTES_WARN_RATIO', 0.8));
        $monthKey  = Carbon::now()->format('Y-m');

        $usage = RouteUsage::where('month_key', $monthKey)->first();

        if (!$usage) {
            return [
                'month'     => $monthKey,
                'count'     => 0,
                'limit'     => $limit,
                'remaining' => $limit,
                'warn_ratio'=> $warnRatio,
                'warn_at'   => (int) floor($limit * $warnRatio),
            ];
        }

        $currentLimit     = $usage->limit ?: $limit;
        $currentWarnRatio = $usage->warn_ratio ?: $warnRatio;

        return [
            'month'     => $usage->month_key,
            'count'     => $usage->count,
            'limit'     => $currentLimit,
            'remaining' => max(0, $currentLimit - $usage->count),
            'warn_ratio'=> $currentWarnRatio,
            'warn_at'   => (int) floor($currentLimit * $currentWarnRatio),
        ];
    }
}
