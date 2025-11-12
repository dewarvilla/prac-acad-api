<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCreacionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        $trim = fn($s) => preg_replace('/\s+/u', ' ', trim((string)$s));
        $this->merge([
            'nombre_practica' => $this->has('nombre_practica') ? $trim($this->input('nombre_practica')) : null,
            'justificacion'   => $this->has('justificacion') ? $trim($this->input('justificacion')) : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'catalogo_id'         => ['required','integer','exists:catalogos,id'],
            'nombre_practica'     => [
                'required','string','max:255',
                Rule::unique('creaciones','nombre_practica')
                    ->where(fn($q) => $q->where('catalogo_id', $this->input('catalogo_id')))
            ],
            'recursos_necesarios' => ['required','string'],
            'justificacion'       => ['required','string'],

            'estado_creacion'            => ['prohibited'],
            'estado_comite_acreditacion' => ['prohibited'],
            'estado_consejo_facultad'    => ['prohibited'],
            'estado_consejo_academico'   => ['prohibited'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_practica.unique' => 'Ya existe una creación con ese nombre en el catálogo indicado.',
        ];
    }
}
