<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCreacionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nivel_academico' => ['required', Rule::in(['pregrado','postgrado'])],
            'facultad' => ['required','string','max:255'],
            'programa_academico' => ['required','string','max:255'],
            'nombre_practica' => ['required','string','max:255'],
            'recursos_necesarios' => ['required','string'],
            'justificacion' => ['required','string'],
            'estado_practica' => ['required', Rule::in(['en_aprobacion','aprobada','creada'])],
            'estado_depart' => ['required', Rule::in(['aprobada','rechazada','pendiente'])],
            'estado_consejo_facultad' => ['required', Rule::in(['aprobada','rechazada','pendiente'])],
            'estado_consejo_academico' => ['required', Rule::in(['aprobada','rechazada','pendiente'])],
            'requiere_transporte' => ['sometimes','boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $map = [
            'nivelAcademico' => 'nivel_academico',
            'programaAcademico' => 'programa_academico',
            'nombrePractica' => 'nombre_practica',
            'recursosNecesarios' => 'recursos_necesarios',
            'estadoPractica' => 'estado_practica',
            'estadoDepart' => 'estado_depart',
            'estadoConsejoFacultad' => 'estado_consejo_facultad',
            'estadoConsejoAcademico' => 'estado_consejo_academico',
            'requiereTransporte' => 'requiere_transporte',
        ];
        $this->merge(collect($map)->mapWithKeys(fn($v,$k)=>[$v=>$this->$k])->filter()->all());
    }
}
