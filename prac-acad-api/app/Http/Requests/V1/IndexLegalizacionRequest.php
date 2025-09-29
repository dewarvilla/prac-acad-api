<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class IndexLegalizacionRequest extends FormRequest
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
        $sortable = ['fecha_legalizacion','-fecha_legalizacion'];

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

            'fecha_legalizacion'  => ['sometimes','date'],
            'estado_depart'       => ['sometimes', Rule::in(['aprobada','rechazada','pendiente'])],
            'estado_postg'        => ['sometimes', Rule::in(['aprobada','rechazada','pendiente'])],
            'estado_logistica'    => ['sometimes', Rule::in(['aprobada','rechazada','pendiente'])],
            'estado_tesoreria'    => ['sometimes', Rule::in(['aprobada','rechazada','pendiente'])],
            'estado_contabilidad' => ['sometimes', Rule::in(['aprobada','rechazada','pendiente'])],
            'programacion_id'     => ['sometimes','integer','min:1'],
        ];
    }
}
