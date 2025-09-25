<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;



class IndexCreacionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'q'        => ['sometimes','string','max:255'],
            'per_page' => ['sometimes','integer','min:1','max:200'],
            'page'     => ['sometimes','integer','min:1'],

            'sort'     => ['sometimes', Rule::in([
                'nombrePractica','-nombrePractica',
                'programaAcademico','-programaAcademico',
                'estadoPractica','-estadoPractica',
                'id','-id'
            ])],

            // Filtros “eq”
            'nombrePractica'        => ['sometimes','string','max:255'],
            'recursosNecesarios'    => ['sometimes','string'],
            'justificacion'         => ['sometimes','string'],
            'estadoPractica'        => ['sometimes', Rule::in(['en_aprobacion','aprobada','creada'])],
            'estadoDepart'          => ['sometimes', Rule::in(['aprobada','rechazada','pendiente'])],
            'estadoConsejoFacultad' => ['sometimes', Rule::in(['aprobada','rechazada','pendiente'])],
            'estadoConsejoAcademico'=> ['sometimes', Rule::in(['aprobada','rechazada','pendiente'])],
            'catalogoId'       => ['sometimes','integer','min:1'],

            // Filtros “like” (recuerda: deben llegar como facultad[lk]=..., etc.)
            'nombrePractica.lk'        => ['sometimes','string','max:255'],
        ];
    }
}