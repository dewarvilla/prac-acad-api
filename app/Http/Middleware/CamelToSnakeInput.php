<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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

    public function handle(Request $request, Closure $next)
    {
        try {
            $ctype = (string) $request->headers->get('content-type', '');

            if (str_contains($ctype, 'application/json')) {
                $raw = $request->getContent();

                if ($raw !== '') {
                    $json = json_decode($raw, true);

                    if (json_last_error() !== JSON_ERROR_NONE) {
                        \Log::warning('CamelToSnakeInput - Error al decodificar JSON', [
                            'path'          => $request->path(),
                            'content_type'  => $ctype,
                            'json_error'    => json_last_error_msg(),
                        ]);
                    } elseif (is_array($json)) {
                        $snake = $this->snakeKeys($json);
                        $request->replace($snake);    
                        $request->json()->replace($snake);
                    }
                }
            } else {
                $all = $request->all();
                if (!empty($all)) {
                    $snake = $this->snakeKeys($all);
                    $request->replace($snake);
                }
            }

            if (!empty($request->query())) {
                $request->query->replace(
                    $this->snakeKeys($request->query())
                );
            }
        } catch (\Throwable $e) {
            \Log::error('CamelToSnakeInput - Excepción durante transformación', [
                'path'         => $request->path(),
                'content_type' => $request->headers->get('content-type'),
                'message'      => $e->getMessage(),
                'trace'        => $e->getTraceAsString(),
            ]);
        }

        return $next($request);
    }
}
