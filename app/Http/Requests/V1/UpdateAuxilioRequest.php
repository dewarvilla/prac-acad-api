<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSalarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user != null && $user->tokenCan('create');
    }

    public function rules(): array
    {
        return [
            'anio' => ['required','integer','min:2024', Rule::unique('anio')],
            'valor' => ['required','integer','min:0'],
        ];
    }
}
