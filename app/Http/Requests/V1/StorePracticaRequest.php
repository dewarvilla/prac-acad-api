<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePracticaRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user != null && $user->tokenCan('create');
    }

    public function rules(): array
    {
        $programa = $this->input('programa_academico');

        return [
            'nombre' => ['required','string','max:255',
                // Unicidad por (nombre, programa_academico)
                Rule::unique('practicas','nombre')->where(fn($q)=>$q->where('programa_academico',$programa))
            ],
            'nivel' => ['required', Rule::in('pregrado', 'Pregrado', 'PREGRADO', 'POSTGRADOS', 'postgrados', 'Postgrados')],
            'facultad' => ['required','string','max:255'],
            'programa_academico' => ['required','string','max:255'],
            'descripcion' => ['required','string'],
            'lugar_de_realizacion' => ['nullable','string','max:255'],
            'justificacion' => ['required','string'],
            'recursos_necesarios' => ['required','string'],

            'estado_practica' => ['nullable', Rule::in('en_aprobacion', 'aprobada', 'rechazada', 'en_ejecucion', 
            'ejecutada', 'en_legalizacion', 'legalizada')],
            'estado_depart' => ['nullable', Rule::in('aprobada', 'rechazada', 'pendiente')],
            'estado_postg' => ['nullable', Rule::in('aprobada', 'rechazada', 'pendiente')],
            'estado_decano' => ['nullable', Rule::in('aprobada', 'rechazada', 'pendiente')],
            'estado_jefe_postg' => ['nullable', Rule::in('aprobada', 'rechazada', 'pendiente')],
            'estado_vice' => ['nullable', Rule::in('aprobada', 'rechazada', 'pendiente')],

            'fecha_finalizacion' => ['required','date'],
            'fecha_solicitud' => ['required','date'],

            'user_id' => ['required','exists:users,id'],
        ];
    }
}

