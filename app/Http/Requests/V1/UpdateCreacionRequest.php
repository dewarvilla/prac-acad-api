<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCreacionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $rules = [
            'catalogo_id'         => ['required','integer','exists:catalogos,id'],

            'nombre_practica' => ['string','max:255'],
            'recursos_necesarios' => ['string'],
            'justificacion' => ['string'],
            'estado_practica' => [Rule::in(['en_aprobacion','aprobada','creada'])],
            'estado_depart' => [Rule::in(['aprobada','rechazada','pendiente'])],
            'estado_consejo_facultad' => [Rule::in(['aprobada','rechazada','pendiente'])],
            'estado_consejo_academico' => [Rule::in(['aprobada','rechazada','pendiente'])],
            'requiere_transporte' => ['boolean'],
        ];

        if ($this->isMethod('patch')) {
            return collect($rules)->map(fn($r)=>array_merge(['sometimes'], $r))->all();
        }

        return $rules;
    }

    protected function prepareForValidation(): void
    {
        $map = [
            'catalogoId' => 'catalogo_id',
            'programaAcademico' => 'programa_academico',
            'nombrePractica' => 'nombre_practica',
            'recursosNecesarios' => 'recursos_necesarios',
            'estadoPractica' => 'estado_practica',
            'estadoDepart' => 'estado_depart',
            'estadoConsejoFacultad' => 'estado_consejo_facultad',
            'estadoConsejoAcademico' => 'estado_consejo_academico',
            'requiereTransporte' => 'requiere_transporte',
        ];
        
        $this->merge(collect($map)->mapWithKeys(fn ($out, $in) => [$out => $this->input($in)])->filter(fn ($v) => !is_null($v))->all());
    }
}
