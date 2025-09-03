<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rule;

class StoreLegalizacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user != null && $user->tokenCan('create');
    }

    public function rules(): array
    {
        return [
            'fecha_legalizacion' => ['required','date'],
            'estado_depart' => ['nullable', Rule::in('aprobada', 'rechazada', 'pendiente')],
            'estado_postg' => ['nullable', Rule::in('aprobada', 'rechazada', 'pendiente')],
            'estado_tesoreria' => ['nullable', Rule::in('aprobada', 'rechazada', 'pendiente')],
            'estado_contabilidad' => ['nullable', Rule::in('aprobada', 'rechazada', 'pendiente')],

            'practica_id' => ['required','exists:practicas,id'],
        ];
    }
}
