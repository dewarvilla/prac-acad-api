<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;

class UsageCounter
{
    protected string $prefix = 'usage:routes';

    protected function keysForMonth(?Carbon $when = null): array
    {
        $when ??= now();
        $ym = $when->format('Y-m'); // p.ej. 2025-10
        return [
            'count' => "{$this->prefix}:{$ym}:count",
            'meta'  => "{$this->prefix}:{$ym}:meta",
        ];
    }

    public function stats(): array
    {
        $limit     = (int) (config('usage.routes_limit') ?? 9000);
        $warnRatio = (float) (config('usage.routes_warn_ratio') ?? 0.8);

        $keys = $this->keysForMonth();
        $count = (int) (Redis::get($keys['count']) ?? 0);

        return [
            'count'      => $count,
            'limit'      => $limit,
            'remaining'  => max(0, $limit - $count),
            'warn_ratio' => $warnRatio,
            'warn_at'    => (int) floor($limit * $warnRatio),
            'month'      => now()->format('Y-m'),
        ];
    }

    /** Intenta reservar 1 unidad; devuelve ['allowed'=>bool, 'stats'=>...] */
    public function reserve(): array
    {
        $limit = (int) (config('usage.routes_limit') ?? 9000);

        $keys = $this->keysForMonth();
        // INCR atómico
        $newCount = (int) Redis::incr($keys['count']);

        // Aseguramos expiración al fin de mes (solo una vez)
        if (!Redis::exists($keys['meta'])) {
            $expiresAt = now()->endOfMonth();
            $ttl = $expiresAt->diffInSeconds(now());
            Redis::setex($keys['meta'], $ttl, '1');
            Redis::expire($keys['count'], $ttl);
        }

        $allowed = $newCount <= $limit;

        // Si superó, revertimos (decremento) y negamos
        if (!$allowed) {
            Redis::decr($keys['count']);
        }

        return [
            'allowed' => $allowed,
            'stats'   => $this->stats(),
        ];
    }
}
