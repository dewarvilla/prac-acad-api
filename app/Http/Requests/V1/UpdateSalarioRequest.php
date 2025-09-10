<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSalarioRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $salario = $this->route('salario');

        $rules = [
            'anio' => ['integer','min:1900', Rule::unique('salarios','anio')->ignore($salario->id ?? null)],
            'valor' => ['numeric','min:0'],
        ];

        if ($this->isMethod('patch')) {
            return collect($rules)->map(fn($r)=>array_merge(['sometimes'], $r))->all();
        }

        return [
            'anio' => ['required','integer','min:1900', Rule::unique('salarios','anio')->ignore($salario->id ?? null)],
            'valor' => ['required','numeric','min:0'],
        ];
    }
}
