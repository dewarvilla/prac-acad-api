<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexParticipanteRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'per_page' => ['sometimes','integer','min:1','max:200'],
            'page'     => ['sometimes','integer','min:1'],
            'sort'     => ['sometimes', Rule::in([
                'numeroIdentificacion','-numeroIdentificacion',
                'nombre','-nombre',
            ])],

            'numeroIdentificacion' => ['sometimes','string','max:100'],
            'tipoParticipante'     => ['sometimes', Rule::in(['estudiante','docente','acompanante'])],
            'discapacidad'         => ['sometimes','boolean'],
            'nombre'               => ['sometimes','string','max:255'],
            'correoInstitucional'  => ['sometimes','string','max:255'],
            'telefono'             => ['sometimes','string','max:50'],
            'programaAcademico'    => ['sometimes','string','max:255'],
            'facultad'             => ['sometimes','string','max:255'],
            'repitente'            => ['sometimes','boolean'],
            'programacionId'       => ['sometimes','integer','min:1'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $map = [
            'numeroIdentificacion' => 'numero_identificacion',
            'tipoParticipante'     => 'tipo_participante',
            'correoInstitucional'  => 'correo_institucional',
            'programaAcademico'    => 'programa_academico',
            'programacionId'       => 'programacion_id',
        ];
        $merge = [];
        foreach ($map as $in => $out) if ($this->has($in)) $merge[$out] = $this->input($in);

        foreach (['discapacidad','repitente'] as $b) {
            if ($this->has($b)) $merge[$b] = filter_var($this->input($b), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

        if ($merge) $this->merge($merge);
    }
}
