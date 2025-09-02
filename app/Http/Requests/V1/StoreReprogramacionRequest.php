<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReprogramacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user != null && $user->tokenCan('create');
    }

    public function rules(): array
    {
        return [
            'fecha_reprogramacion' => ['required','date'],
            'estado_reprogramacion' => ['nullable', Rule::in('aprobada', 'rechazada', 'pendiente')],
            'tipo_reprogramacion' => ['required', Rule::in('normal', 'emergencia')],
            'estado_vice' => ['nullable', Rule::in('aprobada', 'rechazada', 'pendiente')],
            'justificacion' => ['required','string'],
            'practica_id' => ['required','exists:practicas,id'],
        ];
    }
}
