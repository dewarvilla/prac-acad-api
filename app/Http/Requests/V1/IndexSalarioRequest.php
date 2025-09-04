<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexSalarioRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'per_page' => ['sometimes','integer','min:1','max:200'],
            'page'     => ['sometimes','integer','min:1'],
            'sort'     => ['sometimes', Rule::in([
                'anio','-anio','valor','-valor',
            ])],

            'anio'  => ['sometimes','integer','min:1900','max:3000'],
            'valor' => ['sometimes','numeric','min:0'],
        ];
    }
}

