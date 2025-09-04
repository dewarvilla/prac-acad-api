<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexReprogramacionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'per_page' => ['sometimes','integer','min:1','max:200'],
            'page'     => ['sometimes','integer','min:1'],
            'sort'     => ['sometimes', Rule::in([
                'fechaReprogramacion','-fechaReprogramacion',
            ])],

            'fechaReprogramacion'  => ['sometimes','date'],
            'estadoReprogramacion' => ['sometimes', Rule::in(['aprobada','rechazada','pendiente'])],
            'tipoReprogramacion'   => ['sometimes', Rule::in(['normal','emergencia'])],
            'estadoVice'           => ['sometimes', Rule::in(['aprobada','rechazada','pendiente'])],
            'justificacion'        => ['sometimes','string','max:1000'],
            'programacionId'       => ['sometimes','integer','min:1'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $map = [
            'fechaReprogramacion'  => 'fecha_reprogramacion',
            'estadoReprogramacion' => 'estado_reprogramacion',
            'tipoReprogramacion'   => 'tipo_reprogramacion',
            'estadoVice'           => 'estado_vice',
            'programacionId'       => 'programacion_id',
        ];
        $merge = [];
        foreach ($map as $in => $out) if ($this->has($in)) $merge[$out] = $this->input($in);
        if ($merge) $this->merge($merge);
    }
}
