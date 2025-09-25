<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexLegalizacionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'per_page' => ['sometimes','integer','min:1','max:200'],
            'page'     => ['sometimes','integer','min:1'],
            'sort'     => ['sometimes', Rule::in([
                'fechaLegalizacion','-fechaLegalizacion',
            ])],

            'fechaLegalizacion' => ['sometimes','date'],
            'estadoDepart'      => ['sometimes', Rule::in(['aprobada','rechazada','pendiente'])],
            'estadoPostg'       => ['sometimes', Rule::in(['aprobada','rechazada','pendiente'])],
            'estadoLogistica'   => ['sometimes', Rule::in(['aprobada','rechazada','pendiente'])],
            'estadoTesoreria'   => ['sometimes', Rule::in(['aprobada','rechazada','pendiente'])],
            'estadoContabilidad'=> ['sometimes', Rule::in(['aprobada','rechazada','pendiente'])],
            'programacionId'    => ['sometimes','integer','min:1'],
        ];
    }
}
