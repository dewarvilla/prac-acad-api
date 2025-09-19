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
            'q'        => ['sometimes','string','max:255'],
            'per_page' => ['sometimes','integer','min:1','max:200'],
            'page'     => ['sometimes','integer','min:1'],

            'sort'     => ['sometimes', Rule::in([
                'nombrePractica','-nombrePractica',
                'programaAcademico','-programaAcademico',
                'estadoPractica','-estadoPractica',
                'id','-id'
            ])],

            // Filtros “eq”
            'nombrePractica'        => ['sometimes','string','max:255'],
            'recursosNecesarios'    => ['sometimes','string'],
            'justificacion'         => ['sometimes','string'],
            'estadoPractica'        => ['sometimes', Rule::in(['en_aprobacion','aprobada','creada'])],
            'estadoDepart'          => ['sometimes', Rule::in(['aprobada','rechazada','pendiente'])],
            'estadoConsejoFacultad' => ['sometimes', Rule::in(['aprobada','rechazada','pendiente'])],
            'estadoConsejoAcademico'=> ['sometimes', Rule::in(['aprobada','rechazada','pendiente'])],
            'catalogoId'       => ['sometimes','integer','min:1'],

            // Filtros “like” (recuerda: deben llegar como facultad[lk]=..., etc.)
            'nombrePractica.lk'        => ['sometimes','string','max:255'],
        ];
    }

    protected function prepareForValidation(): void
    {
        // (Opcional) normaliza q y elimínala si viene vacía
        if ($this->has('q')) {
            $q = trim((string) $this->input('q'));
            if ($q === '') {
                // si no quieres validarla cuando esté vacía:
                $this->request->remove('q');
            } else {
                $this->merge(['q' => $q]);
            }
        }

        $map = [
            'nivelAcademico'        => 'nivel_academico',
            'programaAcademico'     => 'programa_academico',
            'nombrePractica'        => 'nombre_practica',
            'catalogoId'        => 'catalogo_id',
            'recursosNecesarios'    => 'recursos_necesarios',
            'estadoPractica'        => 'estado_practica',
            'estadoDepart'          => 'estado_depart',
            'estadoConsejoFacultad' => 'estado_consejo_facultad',
            'estadoConsejoAcademico'=> 'estado_consejo_academico',
            'requiereTransporte'    => 'requiere_transporte',
        ];
        $merge = [];
        foreach ($map as $in => $out) {
            if ($this->has($in)) $merge[$out] = $this->input($in);
        }

        if ($merge) $this->merge($merge);
    }
}