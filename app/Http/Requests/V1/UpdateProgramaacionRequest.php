<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProgramacionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = (int) optional($this->route('programacion'))->id;

        $rules = [
            'nombre' => ['string','max:255'],
            'nivel' => [Rule::in(['pregrado','posgrado'])],
            'facultad' => ['string','max:255'],
            'programa_academico' => ['string','max:255'],
            'descripcion' => ['string'],
            'lugar_de_realizacion' => ['nullable','string','max:255'],
            'justificacion' => ['string'],
            'recursos_necesarios' => ['string'],

            'estado_practica' => [Rule::in(['en_aprobacion','aprobada','rechazada','en_ejecucion','ejecutada','en_legalizacion','legalizada'])],
            'estado_depart' => [Rule::in(['aprobada','rechazada','pendiente'])],
            'estado_postg' => [Rule::in(['aprobada','rechazada','pendiente'])],
            'estado_decano' => [Rule::in(['aprobada','rechazada','pendiente'])],
            'estado_jefe_postg' => [Rule::in(['aprobada','rechazada','pendiente'])],
            'estado_vice' => [Rule::in(['aprobada','rechazada','pendiente'])],

            'fecha_inicio' => ['date'],
            'fecha_finalizacion' => ['date'],

            'creacion_id' => ['exists:creaciones,id'],

            // unicidad compuesta
            'unique_nombre_programa' => [
                function($attr,$val,$fail){
                    if ($this->filled('nombre') || $this->filled('programa_academico')) {
                        $nombre = $this->input('nombre', optional($this->route('programacion'))->nombre);
                        $prog   = $this->input('programa_academico', optional($this->route('programacion'))->programa_academico);

                        $exists = \DB::table('programaciones')
                            ->where('nombre', $nombre)
                            ->where('programa_academico', $prog)
                            ->where('id', '!=', optional($this->route('programacion'))->id)
                            ->exists();
                        if ($exists) $fail('La combinación nombre y programa_academico ya existe.');
                    }
                }
            ],
        ];

        if ($this->isMethod('patch')) {
            return collect($rules)->map(fn($r)=>is_array($r)?array_merge(['sometimes'],$r):$r)->all();
        }

        return array_merge($rules, [
            'nombre' => ['required','string','max:255'],
            'nivel' => ['required', Rule::in(['pregrado','posgrado'])],
            'facultad' => ['required','string','max:255'],
            'programa_academico' => ['required','string','max:255'],
            'descripcion' => ['required','string'],
            'lugar_de_realizacion' => ['nullable','string','max:255'],
            'justificacion' => ['required','string'],
            'recursos_necesarios' => ['required','string'],
            'fecha_inicio' => ['required','date'],
            'fecha_finalizacion' => ['required','date','after_or_equal:fecha_inicio'],
            'creacion_id' => ['required','exists:creaciones,id'],
        ]);
    }

    protected function prepareForValidation(): void
    {
        (new StoreProgramacionRequest())->prepareForValidation.call($this);
    }
}


