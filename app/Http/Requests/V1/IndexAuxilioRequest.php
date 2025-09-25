<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexAuxilioRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'per_page' => ['sometimes','integer','min:1','max:200'],
            'page'     => ['sometimes','integer','min:1'],
            'sort'     => ['sometimes', Rule::in([
                'valorTotalAuxilio','-valorTotalAuxilio',
                'numeroTotalEstudiantes','-numeroTotalEstudiantes',
            ])],

            'pernocta'                => ['sometimes','boolean'],
            'distanciasMayor70km'     => ['sometimes','boolean'],
            'fueraCordoba'            => ['sometimes','boolean'],
            'numeroTotalEstudiantes'  => ['sometimes','integer','min:0'],
            'numeroTotalDocentes'     => ['sometimes','integer','min:0'],
            'valorPorDocente'         => ['sometimes','numeric'],
            'valorPorEstudiante'      => ['sometimes','numeric'],
            'valorPorAcompanante'     => ['sometimes','numeric'],
            'programacionId'          => ['sometimes','integer','min:1'],
        ];
    }
}
