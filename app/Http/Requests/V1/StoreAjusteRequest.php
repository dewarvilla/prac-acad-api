<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAjusteRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'fecha_ajuste' => ['required','date'],
            'estado_ajuste' => ['nullable', Rule::in(['aprobada','rechazada','pendiente'])],
            'estado_vice' => ['nullable', Rule::in(['aprobada','rechazada','pendiente'])],
            'estado_jefe_depart' => ['nullable', Rule::in(['aprobada','rechazada','pendiente'])],
            'estado_coordinador_postg' => ['nullable', Rule::in(['aprobada','rechazada','pendiente'])],
            'justificacion' => ['required','string'],
            'programacion_id' => ['sometimes','exists:programaciones,id'],
        ];
    }
}
