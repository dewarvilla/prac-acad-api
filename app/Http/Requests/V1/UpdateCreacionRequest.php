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
            'nivel_academico' => [Rule::in(['pregrado','postgrado'])],
            'facultad' => ['string','max:255'],
            'programa_academico' => ['string','max:255'],
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
        (new StoreCreacionRequest())->prepareForValidation.call($this);
    }
}
