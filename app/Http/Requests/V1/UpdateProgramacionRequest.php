<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Http\Resources\V1\ProgramacionResource;

class UpdateProgramacionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $rules = [
            'creacion_id'         => ['integer','exists:creaciones,id'],
            'nombre_practica'     => ['string','max:255'],
            'descripcion'         => ['string'],
            'lugar_de_realizacion'=> ['nullable','string','max:255'],
            'justificacion'       => ['string'],
            'recursos_necesarios' => ['string'],
            'requiere_transporte' => ['boolean'],
            
            'numero_estudiantes' => ['integer', 'between:1,100'],

            'estado_practica'     => [Rule::in(['en_aprobacion','aprobada','rechazada','en_ejecucion','ejecutada','en_legalizacion','legalizada'])],
            'estado_depart'       => [Rule::in(['aprobada','rechazada','pendiente'])],
            'estado_postg'        => [Rule::in(['aprobada','rechazada','pendiente'])],
            'estado_decano'       => [Rule::in(['aprobada','rechazada','pendiente'])],
            'estado_jefe_postg'   => [Rule::in(['aprobada','rechazada','pendiente'])],
            'estado_vice'         => [Rule::in(['aprobada','rechazada','pendiente'])],

            'fecha_inicio'        => ['date'],
            'fecha_finalizacion'  => ['date','after_or_equal:fecha_inicio'],
        ];

        if ($this->isMethod('patch')) {
            return collect($rules)->map(fn($r)=>is_array($r)?array_merge(['sometimes'],$r):$r)->all();
        }

        return array_merge($rules, [
            'descripcion'         => ['required','string'],
            'justificacion'       => ['required','string'],
            'recursos_necesarios' => ['required','string'],
            'fecha_inicio'        => ['required','date'],
            'fecha_finalizacion'  => ['required','date','after_or_equal:fecha_inicio'],
            'creacion_id'         => ['required','integer','exists:creaciones,id'],
        ]);
    }
}


