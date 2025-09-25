<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateReprogramacionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $rules = [
            'fecha_reprogramacion' => ['date'],
            'estado_reprogramacion' => [Rule::in(['aprobada','rechazada','pendiente'])],
            'tipo_reprogramacion' => [Rule::in(['normal','emergencia'])],
            'estado_vice' => [Rule::in(['aprobada','rechazada','pendiente'])],
            'justificacion' => ['string'],
            'programacion_id' => ['exists:programaciones,id'],
        ];

        if ($this->isMethod('patch')) {
            return collect($rules)->map(fn($r)=>array_merge(['sometimes'], $r))->all();
        }

        return [
            'fecha_reprogramacion' => ['required','date'],
            'estado_reprogramacion' => ['required', Rule::in(['aprobada','rechazada','pendiente'])],
            'tipo_reprogramacion' => ['required', Rule::in(['normal','emergencia'])],
            'estado_vice' => ['required', Rule::in(['aprobada','rechazada','pendiente'])],
            'justificacion' => ['required','string'],
            'programacion_id' => ['required','exists:programaciones,id'],
        ];
    }
}

