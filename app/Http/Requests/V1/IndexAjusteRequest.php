<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexAjusteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'per_page' => ['sometimes','integer','min:1','max:200'],
            'page'     => ['sometimes','integer','min:1'],
            'sort'     => ['sometimes', Rule::in([
                'fechaAjuste','-fechaAjuste',
                'estadoAjuste','-estadoAjuste',
                'estadoVice','-estadoVice',
                'estadoJefeDepart','-estadoJefeDepart',
                'estadoCoordinadorPostg','-estadoCoordinadorPostg',
            ])],

            // filtros
            'fechaAjuste'             => ['sometimes','date'],
            'estadoAjuste'            => ['sometimes', Rule::in(['aprobada','rechazada','pendiente'])],
            'estadoVice'              => ['sometimes', Rule::in(['aprobada','rechazada','pendiente'])],
            'estadoJefeDepart'        => ['sometimes', Rule::in(['aprobada','rechazada','pendiente'])],
            'estadoCoordinadorPostg'  => ['sometimes', Rule::in(['aprobada','rechazada','pendiente'])],
            'justificacion'           => ['sometimes','string','max:1000'],
            'programacionId'          => ['sometimes','integer','min:1'],
        ];
    }
}
