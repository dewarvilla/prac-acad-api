<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreParticipanteRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user != null && $user->tokenCan('create');
    }

    public function rules(): array
    {
        $practicaId = $this->input('practica_id');

        return [
            'numero_identificacion' => ['required','string','max:100',
                // Único por práctica
                Rule::unique('participantes','numero_identificacion')->where(fn($q)=>$q->where('practica_id',$practicaId))
            ],
            'tipo_participante' => ['required', Rule::in('estudiante', 'docente', 'acompañante')],
            'discapacidad' => ['required', 'boolean'],
            'nombre' => ['required','string','max:120'],
            'apellido' => ['required','string','max:120'],
            'correo_institucional' => ['nullable','email','max:255'],
            'telefono' => ['nullable','string','max:50'],
            'programa_academico' => ['required','string','max:255'],
            'facultad' => ['required','string','max:255'],
            'repitente' => ['required', 'boolean'],

            'practica_id' => ['required','exists:practicas,id'],
            'user_id' => ['required','exists:users,id'],
        ];
    }
}
