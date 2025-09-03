<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateParticipanteRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user !== null && $user->tokenCan('update');
    }

    public function rules(): array
    {
        $model = $this->route('participante');
        $id    = is_object($model) ? $model->id : $model;
        $pid   = $this->input('practica_id', is_object($model) ? $model->practica_id : null);

        if ($this->isMethod('patch')) {
            return [
                'numero_identificacion' => [
                    'sometimes','string','max:100',
                    Rule::unique('participantes','numero_identificacion')
                        ->where(fn($q)=>$q->where('practica_id',$pid))
                        ->ignore($id)
                ],
                'tipo_participante' => ['sometimes', Rule::in('estudiante', 'docente', 'acompanante')],
                'discapacidad' => ['sometimes','boolean'],
                'nombre' => ['sometimes','string','max:120'],
                'apellido' => ['sometimes','string','max:120'],
                'correo_institucional' => ['sometimes','nullable','email','max:255'],
                'telefono' => ['sometimes','string','max:50'],
                'programa_academico' => ['sometimes','nullable','string','max:255'],
                'facultad' => ['sometimes','nullable','string','max:255'],
                'repitente' => ['sometimes','boolean'],
                'practica_id' => ['sometimes','exists:practicas,id'],
                'user_id' => ['sometimes','exists:users,id'],
            ];
        }

        return [
            'numero_identificacion' => [
                'required','string','max:100',
                Rule::unique('participantes','numero_identificacion')
                    ->where(fn($q)=>$q->where('practica_id',$pid))
                    ->ignore($id)
            ],
            'tipo_participante' => ['required', Rule::in('estudiante', 'docente', 'acompanante')],
            'discapacidad' => ['required','boolean'],
            'nombre' => ['required','string','max:120'],
            'apellido' => ['required','string','max:120'],
            'correo_institucional' => ['nullable','email','max:255'],
            'telefono' => ['required','string','max:50'],
            'programa_academico' => ['nullable','string','max:255'],
            'facultad' => ['nullable','string','max:255'],
            'repitente' => ['required','boolean'],
            'practica_id' => ['required','exists:practicas,id'],
            'user_id' => ['required','exists:users,id'],
        ];
    }
}
