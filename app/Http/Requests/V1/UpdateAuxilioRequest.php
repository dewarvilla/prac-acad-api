<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAuxilioRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $rules = [
            'pernocta' => ['boolean'],
            'distancias_mayor_70km' => ['boolean'],
            'fuera_cordoba' => ['boolean'],
            'numero_total_estudiantes' => ['integer','min:0'],
            'numero_total_docentes' => ['integer','min:0'],
            'numero_total_acompanantes' => ['integer','min:0'],
            'valor_por_docente' => ['numeric','min:0'],
            'valor_por_estudiante' => ['numeric','min:0'],
            'valor_por_acompanante' => ['numeric','min:0'],
            'valor_total_auxilio' => ['numeric','min:0'],
            'programacion_id' => ['exists:programaciones,id'],
        ];

        if ($this->isMethod('patch')) {
            return collect($rules)->map(fn($r)=>array_merge(['sometimes'], $r))->all();
        }

        return [
            'pernocta' => ['required','boolean'],
            'distancias_mayor_70km' => ['required','boolean'],
            'fuera_cordoba' => ['required','boolean'],
            'numero_total_estudiantes' => ['required','integer','min:0'],
            'numero_total_docentes' => ['required','integer','min:0'],
            'numero_total_acompanantes' => ['required','integer','min:0'],
            'valor_por_docente' => ['required','numeric','min:0'],
            'valor_por_estudiante' => ['required','numeric','min:0'],
            'valor_por_acompanante' => ['required','numeric','min:0'],
            'valor_total_auxilio' => ['required','numeric','min:0'],
            'programacion_id' => ['required','exists:programaciones,id'],
        ];
    }

    protected function prepareForValidation(): void
    {
        (new StoreAuxilioRequest())->prepareForValidation.call($this);
    }
}
