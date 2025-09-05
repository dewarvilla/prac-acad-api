<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSalarioRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'anio' => ['required','integer','min:1900', Rule::unique('salarios','anio')],
            'valor' => ['required','numeric','min:0'],
        ];
    }
}