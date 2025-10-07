<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
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
            'id','-id',
            'programacion_id','-programacion_id',
            'distancia_m','-distancia_m',
            'duracion_s','-duracion_s',
            'numero_peajes','-numero_peajes',
            'valor_peajes','-valor_peajes',
            'orden','-orden',
            'fechacreacion','-fechacreacion',
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

            'programacion_id' => ['sometimes','integer','min:1'],

            'origen_lat'      => ['sometimes','numeric'],
            'origen_lng'      => ['sometimes','numeric'],
            'destino_lat'     => ['sometimes','numeric'],
            'destino_lng'     => ['sometimes','numeric'],
            'origen_desc'     => ['sometimes','string','max:255'],
            'destino_desc'    => ['sometimes','string','max:255'],

            'distancia_m'     => ['sometimes','integer','min:0'],
            'duracion_s'      => ['sometimes','integer','min:0'],
            'numero_peajes'   => ['sometimes','integer','min:0'],
            'valor_peajes'    => ['sometimes','numeric','min:0'],
            'orden'           => ['sometimes','integer','min:1'],

            'estado'          => ['sometimes','boolean'],
        ];
    }
}
