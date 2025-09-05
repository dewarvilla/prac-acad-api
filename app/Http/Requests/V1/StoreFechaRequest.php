<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreFechaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'fecha_apertura_preg' => ['required','date'],
            'fecha_cierre_docente_preg' => ['required','date','after_or_equal:fecha_apertura_preg'],
            'fecha_cierre_jefe_depart' => ['required','date'],
            'fecha_cierre_decano' => ['required','date'],
            'fecha_apertura_postg' => ['required','date'],
            'fecha_cierre_docente_postg' => ['required','date','after_or_equal:fecha_apertura_postg'],
            'fecha_cierre_coordinador_postg' => ['required','date'],
            'fecha_cierre_jefe_postg' => ['required','date'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $map = [
            'fechaAperturaPreg' => 'fecha_apertura_preg',
            'fechaCierreDocentePreg' => 'fecha_cierre_docente_preg',
            'fechaCierreJefeDepart' => 'fecha_cierre_jefe_depart',
            'fechaCierreDecano' => 'fecha_cierre_decano',
            'fechaAperturaPostg' => 'fecha_apertura_postg',
            'fechaCierreDocentePostg' => 'fecha_cierre_docente_postg',
            'fechaCierreCoordinadorPostg' => 'fecha_cierre_coordinador_postg',
            'fechaCierreJefePostg' => 'fecha_cierre_jefe_postg',
        ];
        $this->merge(collect($map)->mapWithKeys(fn($v,$k)=>[$v=>$this->$k])->filter()->all());
    }
}
