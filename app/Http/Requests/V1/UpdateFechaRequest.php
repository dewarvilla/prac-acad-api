<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFechaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $rules = [
            'periodo' => ['string'],
            'fecha_apertura_preg' => ['date'],
            'fecha_cierre_docente_preg' => ['date'],
            'fecha_cierre_jefe_depart' => ['date'],
            'fecha_cierre_decano' => ['date'],
            'fecha_apertura_postg' => ['date'],
            'fecha_cierre_docente_postg' => ['date'],
            'fecha_cierre_coordinador_postg' => ['date'],
            'fecha_cierre_jefe_postg' => ['date'],
        ];

        if ($this->isMethod('patch')) {
            return collect($rules)->map(fn($r)=>array_merge(['sometimes'], $r))->all();
        }

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
            'fechaAperturaPreg'           => 'fecha_apertura_preg',
            'fechaCierreDocentePreg'      => 'fecha_cierre_docente_preg',
            'fechaCierreJefeDepart'       => 'fecha_cierre_jefe_depart',
            'fechaCierreDecano'           => 'fecha_cierre_decano',
            'fechaAperturaPostg'          => 'fecha_apertura_postg',
            'fechaCierreDocentePostg'     => 'fecha_cierre_docente_postg',
            'fechaCierreCoordinadorPostg' => 'fecha_cierre_coordinador_postg',
            'fechaCierreJefePostg'        => 'fecha_cierre_jefe_postg',
        ];

        $merge = [];
        foreach ($map as $camel => $snake) {
            if ($this->has($camel)) {
                $merge[$snake] = $this->input($camel);
            }
        }
        if (!empty($merge)) {
            $this->merge($merge);
        }
    }
}
