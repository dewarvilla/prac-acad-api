<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCreacionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        $trim = fn($s) => preg_replace('/\s+/u', ' ', trim((string)$s));
        $merge = [];
        if ($this->has('nombre_practica'))     $merge['nombre_practica'] = $trim($this->input('nombre_practica'));
        if ($this->has('justificacion'))       $merge['justificacion']   = $trim($this->input('justificacion'));
        if ($merge) $this->merge($merge);
    }

    public function rules(): array
    {
        $id = $this->route('creacion')?->id;

        $base = [
            'catalogo_id'         => ['integer','exists:catalogos,id'],
            'nombre_practica'     => ['string','max:255'],
            'recursos_necesarios' => ['string'],
            'justificacion'       => ['string'],

            'estado_creacion'            => ['prohibited'],
            'estado_comite_acreditacion' => ['prohibited'],
            'estado_consejo_facultad'    => ['prohibited'],
            'estado_consejo_academico'   => ['prohibited'],
        ];

        if ($this->filled('nombre_practica') || $this->filled('catalogo_id') || $this->isMethod('put')) {
            $catalogoId = $this->input('catalogo_id', optional($this->route('creacion'))->catalogo_id);
            $base['nombre_practica'][] = Rule::unique('creaciones','nombre_practica')
                ->where(fn($q) => $q->where('catalogo_id', $catalogoId))
                ->ignore($id);
        }

        if ($this->isMethod('patch')) {
            return collect($base)->map(fn($r)=>array_merge(['sometimes'], $r))->all();
        }

        return array_merge($base, [
            'catalogo_id'         => ['required','integer','exists:catalogos,id'],
            'nombre_practica'     => ['required','string','max:255'],
            'recursos_necesarios' => ['required','string'],
            'justificacion'       => ['required','string'],
        ]);
    }
}
