<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLegalizacionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $estadoTri = ['aprobada','rechazada','pendiente'];

        return [
            'fecha_legalizacion' => ['required','date'],
            'estado_depart' => ['nullable', Rule::in($estadoTri)],
            'estado_postg' => ['nullable', Rule::in($estadoTri)],
            'estado_logistica' => ['nullable', Rule::in($estadoTri)],
            'estado_tesoreria' => ['nullable', Rule::in($estadoTri)],
            'estado_contabilidad' => ['nullable', Rule::in($estadoTri)],
            'programacion_id' => ['sometimes','exists:programaciones,id'],
        ];
    }
}

