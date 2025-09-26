<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;

class CamelToSnakeInput
{
    public function handle($request, Closure $next)
    {
        // Body JSON
        if ($request->isJson()) {
            $json = $request->json()->all();
            $request->json()->replace($this->snakeKeys($json));
        }

        // Form-data / x-www-form-urlencoded
        if (!empty($request->all())) {
            $request->replace($this->snakeKeys($request->all()));
        }

        // Query string (?fooBar=1)
        if (!empty($request->query())) {
            $request->query->replace($this->snakeKeys($request->query()));
        }

        // Logs de diagnóstico
        \Log::info('CamelToSnakeInput HIT (BEFORE next)', [
            'is_json'      => $request->isJson(),
            'content_type' => $request->headers->get('content-type'),
            'raw_json'     => $request->getContent(),
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
