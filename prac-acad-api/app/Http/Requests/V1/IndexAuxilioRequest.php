<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class IndexAuxilioRequest extends FormRequest
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
            'valor_total_auxilio','-valor_total_auxilio',
            'numero_total_estudiantes','-numero_total_estudiantes',
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

            'pernocta'                 => ['sometimes','boolean'],
            'distancias_mayor_70km'    => ['sometimes','boolean'],
            'fuera_cordoba'            => ['sometimes','boolean'],
            'numero_total_estudiantes' => ['sometimes','integer','min:0'],
            'numero_total_docentes'    => ['sometimes','integer','min:0'],
            'valor_por_docente'        => ['sometimes','numeric'],
            'valor_por_estudiante'     => ['sometimes','numeric'],
            'valor_por_acompanante'    => ['sometimes','numeric'],
            'programacion_id'          => ['sometimes','integer','min:1'],
        ];
    }
}
