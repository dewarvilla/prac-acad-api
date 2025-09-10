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

    protected function prepareForValidation(): void
    {
        $map = [
            'numeroIdentificacion' => 'numero_identificacion',
            'correoInstitucional' => 'correo_institucional',
            'programaAcademico' => 'programa_academico',
            'tipoParticipante' => 'tipo_participante',
            'programacionId' => 'programacion_id',
        ];
        
        $this->merge(collect($map)->mapWithKeys(fn ($out, $in) => [$out => $this->input($in)])->filter(fn ($v) => !is_null($v))->all());
    }
}
