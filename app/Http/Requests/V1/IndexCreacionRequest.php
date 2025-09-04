<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexCreacionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'per_page' => ['sometimes','integer','min:1','max:200'],
            'page'     => ['sometimes','integer','min:1'],
            'sort'     => ['sometimes', Rule::in([
                'nombrePractica','-nombrePractica',
            ])],

            'nivelAcademico'        => ['sometimes', Rule::in(['pregrado','postgrado'])],
            'facultad'              => ['sometimes','string','max:255'],
            'programaAcademico'     => ['sometimes','string','max:255'],
            'nombrePractica'        => ['sometimes','string','max:255'],
            'recursosNecesarios'    => ['sometimes','string'],
            'justificacion'         => ['sometimes','string'],
            'estadoPractica'        => ['sometimes', Rule::in(['en_aprobacion','aprobada','creada'])],
            'estadoDepart'          => ['sometimes', Rule::in(['aprobada','rechazada','pendiente'])],
            'estadoConsejoFacultad' => ['sometimes', Rule::in(['aprobada','rechazada','pendiente'])],
            'estadoConsejoAcademico'=> ['sometimes', Rule::in(['aprobada','rechazada','pendiente'])],
            'requiereTransporte'    => ['sometimes','boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $map = [
            'nivelAcademico'        => 'nivel_academico',
            'programaAcademico'     => 'programa_academico',
            'nombrePractica'        => 'nombre_practica',
            'recursosNecesarios'    => 'recursos_necesarios',
            'estadoPractica'        => 'estado_practica',
            'estadoDepart'          => 'estado_depart',
            'estadoConsejoFacultad' => 'estado_consejo_facultad',
            'estadoConsejoAcademico'=> 'estado_consejo_academico',
            'requiereTransporte'    => 'requiere_transporte',
        ];
        $merge = [];
        foreach ($map as $in => $out) if ($this->has($in)) $merge[$out] = $this->input($in);

        if ($this->has('requiereTransporte')) {
            $merge['requiere_transporte'] = filter_var($this->input('requiereTransporte'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

        if ($merge) $this->merge($merge);
    }
}

