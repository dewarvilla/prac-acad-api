<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreAuxilioRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user != null && $user->tokenCan('create');
    }

    public function rules(): array
    {
        return [
            'pernocta' => ['required', 'boolean'],
            'distancias_mayor_70km' => ['required', 'boolean'],
            'fuera_cordoba' => ['required', 'boolean'],

            'numero_total_estudiantes' => ['required','integer','min:0'],
            'numero_total_docentes' => ['required','integer','min:0'],
            'numero_total_acompanantes' => ['nullable','integer','min:0'],

            'valor_por_docente' => ['required','numeric','min:0'],
            'valor_por_estudiante' => ['required','numeric','min:0'],
            'valor_por_acompanante' => ['nullable','numeric','min:0'],
            'valor_total_auxilio' => ['required','numeric','min:0'],

            'practica_id' => ['required','exists:practicas,id'],
        ];
    }
}
