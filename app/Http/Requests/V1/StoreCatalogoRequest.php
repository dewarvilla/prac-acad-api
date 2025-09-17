<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCatalogoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nivel_academico' => ['required', Rule::in(['pregrado','postgrado'])],
            'facultad' => ['required','string','max:255'],
            'programa_academico' => ['required','string','max:255'],
            'unique_programa_academico' => [
                function($attr,$val,$fail){
                    $exists = \DB::table('catalogos')
                        ->where('facultad', $this->facultad)
                        ->where('programa_academico', $this->programa_academico)
                        ->exists();
                    if ($exists) $fail('La combinación facultad y programa_academico ya existe.');
                }
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $map = [
            'nivelAcademico' => 'nivel_academico',
            'programaAcademico' => 'programa_academico',
            'nombrePractica' => 'nombre_practica',
        ];
        
        $this->merge(collect($map)->mapWithKeys(fn ($out, $in) => [$out => $this->input($in)])->filter(fn ($v) => !is_null($v))->all());
    }
}
