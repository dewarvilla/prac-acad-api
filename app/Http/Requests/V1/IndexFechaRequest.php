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
            'q'        => ['sometimes','string','max:255'],
            'per_page' => ['sometimes','integer','min:1','max:200'],
            'page'     => ['sometimes','integer','min:1'],
            'sort'     => ['sometimes', Rule::in(['id','-id','periodo','-periodo','fechaAperturaPreg',
             '-fechaAperturaPreg', 'fechaCierreDocentePreg', '-fechaCierreDocentePreg', 'fechaAperturaPostg',
             '-fechaAperturaPostg', 'fechaCierreDocentePostg', '-fechaCierreDocentePostg'])],

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
}