<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexFechaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'per_page' => ['sometimes','integer','min:1','max:200'],
            'page'     => ['sometimes','integer','min:1'],
            'sort'     => ['sometimes', Rule::in(['id','-id','periodo','-periodo'])],

            'periodo'    => ['sometimes'], 
            'periodo.lk' => ['sometimes','string'],
            'fechaAperturaPreg'           => ['sometimes','date'],
            'fechaCierreDocentePreg'      => ['sometimes','date'],
            'fechaCierreJefeDepart'       => ['sometimes','date'],
            'fechaCierreDecano'           => ['sometimes','date'],
            'fechaAperturaPostg'          => ['sometimes','date'],
            'fechaCierreDocentePostg'     => ['sometimes','date'],
            'fechaCierreCoordinadorPostg' => ['sometimes','date'],
            'fechaCierreJefePostg'        => ['sometimes','date'],
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
            'fechaCierreJefePostg' => 'fecha_cierre_jefe_postg'
        ];
        $merge = [];
        foreach ($map as $in => $out) if ($this->has($in)) $merge[$out] = $this->input($in);
        if ($merge) $this->merge($merge);
    }
}

