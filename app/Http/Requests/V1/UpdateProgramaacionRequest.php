<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProgramacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user !== null && $user->tokenCan('update');
    }

    public function rules(): array
    {
        $model = $this->route('programacion');               // model o id
        $id    = is_object($model) ? $model->id : $model;

        // Para unico (nombre por programa_academico)
        $programa = $this->input('programa_academico', is_object($model) ? $model->programa_academico : null);

        if ($this->isMethod('patch')) {
            return [
                'nombre'               => [
                    'sometimes','string','max:255',
                    Rule::unique('practicas','nombre')
                        ->where(fn($q) => $q->where('programa_academico', $programa))
                        ->ignore($id)
                ],
                'nivel' => ['sometimes', Rule::in('pregrado', 'postgrado')],
                'facultad' => ['sometimes','string','max:255'],
                'programa_academico' => ['sometimes','string','max:255'],
                'descripcion' => ['sometimes','string'],
                'lugar_de_realizacion' => ['sometimes','nullable','string','max:255'],
                'justificacion' => ['sometimes','string'],
                'recursos_necesarios' => ['sometimes','string'],

                'estado_practica' => ['sometimes', Rule::in('en_aprobacion', 'aprobada', 'rechazada', 
                'en_ejecucion', 'ejecutada', 'en_legalizacion', 'legalizada')],
                'estado_depart' => ['sometimes', Rule::in('aprobada', 'rechazada', 'pendiente')],
                'estado_postg' => ['sometimes', Rule::in('aprobada', 'rechazada', 'pendiente')],
                'estado_decano' => ['sometimes', Rule::in('aprobada', 'rechazada', 'pendiente')],
                'estado_jefe_postg' => ['sometimes', Rule::in('aprobada', 'rechazada', 'pendiente')],
                'estado_vice' => ['sometimes', Rule::in('aprobada', 'rechazada', 'pendiente')],

                'fecha_solicitud' => ['sometimes','date'],
                'fecha_finalizacion' => ['sometimes','date'],
                'user_id' => ['sometimes','exists:users,id'],


                

            ];
        }

        // PUT = reemplazo completo
        return [
            'nombre' => [
                'required','string','max:255',
                Rule::unique('practicas','nombre')
                    ->where(fn($q) => $q->where('programa_academico', $programa))
                    ->ignore($id)
            ],
            'nivel' => ['required', Rule::in('pregrado', 'postgrado')],
            'facultad' => ['required','string','max:255'],
            'programa_academico' => ['required','string','max:255'],
            'descripcion' => ['required','string'],
            'lugar_de_realizacion' => ['nullable','string','max:255'],
            'justificacion' => ['required','string'],
            'recursos_necesarios' => ['required','string'],

            'estado_practica' => ['nullable', Rule::in('en_aprobacion', 'aprobada', 'rechazada', 
                'en_ejecucion', 'ejecutada', 'en_legalizacion', 'legalizada')],
            'estado_depart' => ['nullable', Rule::in('aprobada', 'rechazada', 'pendiente')],
            'estado_postg' => ['nullable', Rule::in('aprobada', 'rechazada', 'pendiente')],
            'estado_decano' => ['nullable', Rule::in('aprobada', 'rechazada', 'pendiente')],
            'estado_jefe_postg' => ['nullable', Rule::in('aprobada', 'rechazada', 'pendiente')],
            'estado_vice' => ['nullable', Rule::in('aprobada', 'rechazada', 'pendiente')],

            'fecha_solicitud' => ['required','date'],
            'fecha_finalizacion' => ['required','date'],
            'user_id' => ['required','exists:users,id'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $map = [
            'programaAcademico' => 'programa_academico',
            'lugarDeRealizacion' => 'lugar_de_realizacion',
            'estadoPractica' => 'estado_practica',
            'estadoDepart' => 'estado_depart',
            'estadoPostg' => 'estado_postg',
            'estadoDecano' => 'estado_decano',
            'estadoJefePostg' => 'estado_jefe_postg',
            'estadoVice' => 'estado_vice',
            'fechaSolicitud' => 'fecha_solicitud',
            'fechaFinalizacion' => 'fecha_finalizacion',
            'userId' => 'user_id',
        ];

        $merge = [];
        foreach ($map as $in => $out) {
            if ($this->has($in)) {
                $merge[$out] = $this->input($in);
            }
        }
        if ($merge) $this->merge($merge);
    }
}

