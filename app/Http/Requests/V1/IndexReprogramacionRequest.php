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
}
