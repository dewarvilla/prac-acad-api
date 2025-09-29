<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;

class CamelToSnakeInput
{
    // app/Http/Middleware/CamelToSnakeInput.php
    public function handle($request, Closure $next)
    {
        \Log::info('CamelToSnakeInput >>> ENTRO', [
            'is_json' => $request->isJson(),
            'ct' => $request->headers->get('content-type'),
            'raw_start' => substr(trim($request->getContent()),0,1),
        ]);
        // 1) Intentar tratar el body como JSON aunque falte Content-Type
        $raw = $request->getContent();
        $looksJson = is_string($raw) && $raw !== '' && in_array(substr(trim($raw), 0, 1), ['{', '[']);
        $decoded = null;

        if ($request->isJson() || $looksJson) {
            try {
                $decoded = $request->isJson()
                    ? $request->json()->all()
                    : json_decode($raw, true, flags: JSON_THROW_ON_ERROR);
            } catch (\Throwable $e) {
                $decoded = null; // no era JSON válido
            }
        }

        if ($request->isJson()) {
            $data = json_decode($request->getContent(), true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
                $snake = $this->snakeKeys($data);

                // Reemplazar en los tres lugares:
                $request->json()->replace($snake);     // para $request->json()
                $request->request->replace($snake);    // para $request->all()
                $request->replace($snake);             // fuerza que all() respete snake_case
            }
        }
        // 3) Query string
        if (!empty($request->query())) {
            $request->query->replace($this->snakeKeys($request->query()));
        }

        \Log::info('CamelToSnakeInput HIT', [
            'is_json'      => $request->isJson(),
            'content_type' => $request->headers->get('content-type'),
            'raw_starts'   => substr(trim($raw), 0, 1),
            'all'          => $request->all(),
            'json'         => $request->isJson() ? $request->json()->all() : null,
            'query'        => $request->query(),
        ]);

        return $next($request);
    }
        private function snakeKeys($value)
        {
            if (is_array($value)) {
                $out = [];
                foreach ($value as $k => $v) {
                    $nk = is_string($k) ? Str::snake($k) : $k;
                    $out[$nk] = $this->snakeKeys($v);
                }
                return $out;
            }
            return $value;
        }
}
