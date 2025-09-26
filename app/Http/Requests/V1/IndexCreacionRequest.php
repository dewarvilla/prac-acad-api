<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class IndexCreacionRequest extends FormRequest
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
            'nombre_practica','-nombre_practica',
            'programa_academico','-programa_academico',
            'estado_practica','-estado_practica',
            'id','-id',
        ];

        return [
            'q'        => ['sometimes','string','max:255'],
            'per_page' => ['sometimes','integer','min:1','max:200'],
            'page'     => ['sometimes','integer','min:1'],

            'sort'     => ['sometimes', function($attr,$value,$fail) use ($sortable){
                foreach (explode(',', (string)$value) as $p) {
                    if (!in_array(trim($p), $sortable, true)) {
                        return $fail("El valor de sort '{$p}' no es permitido.");
                    }
                }
            }],

            'nombre_practica'         => ['sometimes','string','max:255'],
            'recursos_necesarios'     => ['sometimes','string'],
            'justificacion'           => ['sometimes','string'],
            'estado_practica'         => ['sometimes', Rule::in(['en_aprobacion','aprobada','creada'])],
            'estado_depart'           => ['sometimes', Rule::in(['aprobada','rechazada','pendiente'])],
            'estado_consejo_facultad' => ['sometimes', Rule::in(['aprobada','rechazada','pendiente'])],
            'estado_consejo_academico'=> ['sometimes', Rule::in(['aprobada','rechazada','pendiente'])],
            'catalogo_id'             => ['sometimes','integer','min:1'],

            'nombre_practica.lk'      => ['sometimes','string','max:255'],
        ];
    }
}
