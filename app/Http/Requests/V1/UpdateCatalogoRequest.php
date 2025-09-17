<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCatalogoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $rules = [
            'nivel_academico' => [Rule::in(['pregrado','postgrado'])],
            'facultad' => ['string','max:255'],
            'programa_academico' => ['string','max:255'],
        ];

        if ($this->isMethod('patch')) {
            return collect($rules)->map(fn($r)=>array_merge(['sometimes'], $r))->all();
        }

        return [
            'nivel_academico' => ['required', Rule::in(['pregrado','postgrado'])],
            'facultad' => ['required','string','max:255'],
            'programa_academico' => ['required','string','max:255'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $map = [
            'nivelAcademico' => 'nivel_academico',
            'programaAcademico' => 'programa_academico',
            'nombrePractica' => 'nombre_practica',
            'catalgoId' => 'catalogo_id',
        ];
        
        $this->merge(collect($map)->mapWithKeys(fn ($out, $in) => [$out => $this->input($in)])->filter(fn ($v) => !is_null($v))->all());
    }
}
