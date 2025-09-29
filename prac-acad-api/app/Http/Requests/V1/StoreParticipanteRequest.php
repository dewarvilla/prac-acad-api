<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreParticipanteRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'numero_identificacion' => ['required','string','max:100'],
            'discapacidad' => ['required','boolean'],
            'nombre' => ['required','string','max:255'],
            'correo_institucional' => ['nullable','email','max:255'],
            'telefono' => ['required','string','max:50'],
            'programa_academico' => ['nullable','string','max:255'],
            'facultad' => ['nullable','string','max:255'],
            'repitente' => ['required','boolean'],
            'tipo_participante' => ['required', Rule::in(['docente','estudiante','acompanante'])],
            'programacion_id' => ['sometimes','exists:programaciones,id'],
        ];
    }
}
