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
            'q'        => ['sometimes','string','max:255'],
            'per_page' => ['sometimes','integer','min:1','max:200'],
            'page'     => ['sometimes','integer','min:1'],

            'sort'     => ['sometimes', Rule::in([
                'id', '-id',
                'anio','-anio',
                'valor','-valor'
            ])],

            'anio'  => ['sometimes','integer','min:1900','max:3000'],
            'valor' => ['sometimes','numeric','min:0'],
        ];
    }

    protected function prepareForValidation(): void
    {
        // (Opcional) normaliza q y elimínala si viene vacía
        if ($this->has('q')) {
            $q = trim((string) $this->input('q'));
            if ($q === '') {
                // si no quieres validarla cuando esté vacía:
                $this->request->remove('q');
            } else {
                $this->merge(['q' => $q]);
            }
        }

        $map = [];
        $merge = [];
        foreach ($map as $in => $out) {
            if ($this->has($in)) $merge[$out] = $this->input($in);
        }

        if ($merge) $this->merge($merge);
    }
}

