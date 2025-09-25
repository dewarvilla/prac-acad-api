<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexRutaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'per_page' => ['sometimes','integer','min:1','max:200'],
            'page'     => ['sometimes','integer','min:1'],
            'sort'     => ['sometimes', Rule::in([
                'numeroRecorridos','-numeroRecorridos',
                'numeroPeajes','-numeroPeajes'
            ])],

            'latitudSalidas'       => ['sometimes','string','max:255'],
            'latitudLlegadas'      => ['sometimes','string','max:255'],
            'numeroRecorridos'     => ['sometimes','integer','min:0'],
            'numeroPeajes'         => ['sometimes','integer','min:0'],
            'valorPeajes'          => ['sometimes','numeric'],
            'distanciaTrayectosKm' => ['sometimes','numeric'],
            'rutaSalida'           => ['sometimes','string','max:255'],
            'rutaLlegada'          => ['sometimes','string','max:255'],
            'programacionId'       => ['sometimes','integer','min:1'],
        ];
    }
}
