<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class IndexAjusteRequest extends FormRequest
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
            'fecha_ajuste','-fecha_ajuste',
            'estado_ajuste','-estado_ajuste',
            'estado_vice','-estado_vice',
            'estado_jefe_depart','-estado_jefe_depart',
            'estado_coordinador_postg','-estado_coordinador_postg',
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

            'fecha_ajuste'            => ['sometimes','date'],
            'estado_ajuste'           => ['sometimes', Rule::in(['aprobada','rechazada','pendiente'])],
            'estado_vice'             => ['sometimes', Rule::in(['aprobada','rechazada','pendiente'])],
            'estado_jefe_depart'      => ['sometimes', Rule::in(['aprobada','rechazada','pendiente'])],
            'estado_coordinador_postg'=> ['sometimes', Rule::in(['aprobada','rechazada','pendiente'])],
            'justificacion'           => ['sometimes','string','max:1000'],
            'programacion_id'         => ['sometimes','integer','min:1'],
        ];
    }
}
