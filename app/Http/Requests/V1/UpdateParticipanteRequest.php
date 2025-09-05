<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateParticipanteRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $rules = [
            'numero_identificacion' => ['string','max:100'],
            'discapacidad' => ['boolean'],
            'nombre' => ['string','max:255'],
            'correo_institucional' => ['nullable','email','max:255'],
            'telefono' => ['string','max:50'],
            'programa_academico' => ['nullable','string','max:255'],
            'facultad' => ['nullable','string','max:255'],
            'repitente' => ['boolean'],
            'tipo_participante' => [Rule::in(['docente','estudiante','acompanante'])],
            'programacion_id' => ['exists:programaciones,id'],
        ];

        if ($this->isMethod('patch')) {
            return collect($rules)->map(fn($r)=>array_merge(['sometimes'], $r))->all();
        }

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
            'programacion_id' => ['required','exists:programaciones,id'],
        ];
    }

    protected function prepareForValidation(): void
    {
        (new StoreParticipanteRequest())->prepareForValidation.call($this);
    }
}
