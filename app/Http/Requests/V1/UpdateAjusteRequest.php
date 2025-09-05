<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAjusteRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $rules = [
            'fecha_ajuste' => ['date'],
            'estado_ajuste' => [Rule::in(['aprobada','rechazada','pendiente'])],
            'estado_vice' => [Rule::in(['aprobada','rechazada','pendiente'])],
            'estado_jefe_depart' => [Rule::in(['aprobada','rechazada','pendiente'])],
            'estado_coordinador_postg' => [Rule::in(['aprobada','rechazada','pendiente'])],
            'justificacion' => ['string'],
            'programacion_id' => ['exists:programaciones,id'],
        ];

        if ($this->isMethod('patch')) {
            return collect($rules)->map(fn($r)=>array_merge(['sometimes'], $r))->all();
        }

        return [
            'fecha_ajuste' => ['required','date'],
            'estado_ajuste' => ['required', Rule::in(['aprobada','rechazada','pendiente'])],
            'estado_vice' => ['required', Rule::in(['aprobada','rechazada','pendiente'])],
            'estado_jefe_depart' => ['required', Rule::in(['aprobada','rechazada','pendiente'])],
            'estado_coordinador_postg' => ['required', Rule::in(['aprobada','rechazada','pendiente'])],
            'justificacion' => ['required','string'],
            'programacion_id' => ['required','exists:programaciones,id'],
        ];
    }

    protected function prepareForValidation(): void
    {
        (new StoreAjusteRequest())->prepareForValidation.call($this);
    }
}
