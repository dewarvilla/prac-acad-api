<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;

class CamelToSnakeInput
{
    private function snakeKeys($data)
    {
        if (is_array($data)) {
            $out = [];
            foreach ($data as $key => $value) {
                $newKey = is_string($key) ? Str::snake($key) : $key;
                $out[$newKey] = $this->snakeKeys($value);
            }
            return $out;
        }
        return $data;
    }

    public function handle($request, Closure $next)
    {
        \Log::info('CamelToSnakeInput HIT', [
            'path'  => $request->path(),
            'ctype' => $request->headers->get('content-type'),
        ]);

        $ctype = (string) $request->headers->get('content-type', '');

        // JSON crudo
        if (str_contains($ctype, 'application/json')) {
            $raw  = $request->getContent();
            $json = json_decode($raw, true);
            if (is_array($json)) {
                $snake = $this->snakeKeys($json);
                // IMPORTANTE: reflejar en ambos bags para que el validador lo vea
                $request->replace($snake);           // bag "request" (usado por Validator)
                $request->json()->replace($snake);   // bag "json"
            }
        } else {
            // x-www-form-urlencoded / multipart
            $all = $request->all();
            if (!empty($all)) {
                $snake = $this->snakeKeys($all);
                $request->replace($snake);
            }
        }

        // Query string
        if (!empty($request->query())) {
            $request->query->replace($this->snakeKeys($request->query()));
        }

        return $next($request);
    }
}
