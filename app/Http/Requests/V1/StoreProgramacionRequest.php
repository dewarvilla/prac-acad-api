<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class StoreProgramacionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nombre' => ['required','string','max:255'],
            'nivel' => ['required', Rule::in(['pregrado','posgrado'])],
            'facultad' => ['required','string','max:255'],
            'programa_academico' => ['required','string','max:255'],
            'descripcion' => ['required','string'],
            'lugar_de_realizacion' => ['nullable','string','max:255'],
            'justificacion' => ['required','string'],
            'recursos_necesarios' => ['required','string'],
            'requiere_transporte' => ['required','boolean'],


            'estado_practica' => ['nullable', Rule::in(['en_aprobacion','aprobada','rechazada','en_ejecucion','ejecutada','en_legalizacion','legalizada'])],
            'estado_depart' => ['nullable', Rule::in(['aprobada','rechazada','pendiente'])],
            'estado_postg' => ['nullable', Rule::in(['aprobada','rechazada','pendiente'])],
            'estado_decano' => ['nullable', Rule::in(['aprobada','rechazada','pendiente'])],
            'estado_jefe_postg' => ['nullable', Rule::in(['aprobada','rechazada','pendiente'])],
            'estado_vice' => ['nullable', Rule::in(['aprobada','rechazada','pendiente'])],

            'fecha_inicio' => ['required','date'],
            'fecha_finalizacion' => ['required','date','after_or_equal:fecha_inicio'],

            'creacion_id' => ['required','exists:creaciones,id'],

            // unicidad compuesta: nombre + programa_academico
            'unique_nombre_programa' => [
                function($attr,$val,$fail){
                    $exists = \DB::table('programaciones')
                        ->where('nombre', $this->nombre)
                        ->where('programa_academico', $this->programa_academico)
                        ->exists();
                    if ($exists) $fail('La combinación nombre y programa_academico ya existe.');
                }
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $map = [
            'programaAcademico' => 'programa_academico',
            'lugarDeRealizacion' => 'lugar_de_realizacion',
            'recursosNecesarios' => 'recursos_necesarios',
            'estadoPractica' => 'estado_practica',
            'estadoDepart' => 'estado_depart',
            'estadoPostg' => 'estado_postg',
            'estadoDecano' => 'estado_decano',
            'estadoJefePostg' => 'estado_jefe_postg',
            'estadoVice' => 'estado_vice',
            'fechaInicio' => 'fecha_inicio',
            'fechaFinalizacion' => 'fecha_finalizacion',
            'creacionId' => 'creacion_id',
        ];
        
        $this->merge(collect($map)->mapWithKeys(fn ($out, $in) => [$out => $this->input($in)])->filter(fn ($v) => !is_null($v))->all());
    }
}


