<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class StoreCreacionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nombre_practica' => ['required','string','max:255'],
            'recursos_necesarios' => ['required','string'],
            'justificacion' => ['required','string'],
            'estado_practica' => ['nullable', Rule::in(['en_aprobacion','aprobada','creada'])],
            'estado_depart' => ['nullable', Rule::in(['aprobada','rechazada','pendiente'])],
            'estado_consejo_facultad' => ['nullable', Rule::in(['aprobada','rechazada','pendiente'])],
            'estado_consejo_academico' => ['nullable', Rule::in(['aprobada','rechazada','pendiente'])],

            'catalogo_id'=> ['required','integer','exists:catalogos,id'],
        ];
    }
}
