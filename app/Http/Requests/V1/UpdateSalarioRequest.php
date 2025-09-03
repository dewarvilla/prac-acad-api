<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSalarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user !== null && $user->tokenCan('update');
    }

    public function rules(): array
    {
        $model = $this->route('salario');
        $id = is_object($model) ? $model->id : $model;

        if ($this->isMethod('patch')) {
            return [
                'anio' => [
                    'sometimes','integer','min:1990',
                    Rule::unique('salarios','anio')
                        ->where(fn($q)=>$q->where('practica_id',$pid))
                        ->ignore($id)
                ],
                'valor' => ['sometimes','integer','min:0'],
                'practica_id' => ['sometimes','exists:practicas,id'],
            ];
        }

        return [
            'anio' => [
                'required','integer','min:1990',
                Rule::unique('salarios','anio')
                    ->where(fn($q)=>$q->where('practica_id',$pid))
                    ->ignore($id)
            ],
            'valor' => ['required','integer','min:0'],
            'practica_id' => ['required','exists:practicas,id'],
        ];
    }
}
