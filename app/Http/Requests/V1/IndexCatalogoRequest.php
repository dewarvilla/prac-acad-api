<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexCatalogoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'q'        => ['sometimes','string','max:255'],
            'per_page' => ['sometimes','integer','min:1','max:200'],
            'page'     => ['sometimes','integer','min:1'],

            'sort'     => ['sometimes', Rule::in([
                'facultad','-facultad',
                'id','-id',
                'nivelAcademico','-nivelAcademico',
                'programaAcademico','-programaAcademico'
            ])],

            // Filtros “eq”
            'nivelAcademico'        => ['sometimes', Rule::in(['pregrado','postgrado'])],
            'facultad'              => ['sometimes','string','max:255'],
            'programaAcademico'     => ['sometimes','string','max:255'],

            // Filtros “like” (recuerda: deben llegar como facultad[lk]=..., etc.)
            'facultad.lk'              => ['sometimes','string','max:255'],
            'programaAcademico.lk'     => ['sometimes','string','max:255'],
        ];
    }
}