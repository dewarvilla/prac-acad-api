<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class IndexRutaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        if ($this->has('sort')) {
            $parts = array_map('trim', explode(',', (string)$this->input('sort')));
            $norm  = array_map(function ($p) {
                $desc = Str::startsWith($p, '-');
                $field = Str::snake(ltrim($p, '-'));
                return $desc ? "-{$field}" : $field;
            }, $parts);
            $this->merge(['sort' => implode(',', $norm)]);
        }
    }

    public function rules(): array
    {
        $sortable = [
            'numero_recorridos','-numero_recorridos',
            'numero_peajes','-numero_peajes',
        ];

        return [
            'per_page' => ['sometimes','integer','min:1','max:200'],
            'page'     => ['sometimes','integer','min:1'],
            'sort'     => ['sometimes', function($attr,$value,$fail) use ($sortable){
                foreach (explode(',', (string)$value) as $p) {
                    if (!in_array(trim($p), $sortable, true)) {
                        return $fail("El valor de sort '{$p}' no es permitido.");
                    }
                }
            }],

            'latitud_salidas'        => ['sometimes','string','max:255'],
            'latitud_llegadas'       => ['sometimes','string','max:255'],
            'numero_recorridos'      => ['sometimes','integer','min:0'],
            'numero_peajes'          => ['sometimes','integer','min:0'],
            'valor_peajes'           => ['sometimes','numeric'],
            'distancia_trayectos_km' => ['sometimes','numeric'],
            'ruta_salida'            => ['sometimes','string','max:255'],
            'ruta_llegada'           => ['sometimes','string','max:255'],
            'programacion_id'        => ['sometimes','integer','min:1'],
        ];
    }
}
