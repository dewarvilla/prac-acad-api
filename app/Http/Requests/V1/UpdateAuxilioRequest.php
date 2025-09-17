<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAuxilioRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $rules = [
            'pernocta' => ['boolean'],
            'distancias_mayor_70km' => ['boolean'],
            'fuera_cordoba' => ['boolean'],
            'valor_por_docente' => ['numeric','min:0'],
            'valor_por_estudiante' => ['numeric','min:0'],
            'valor_por_acompanante' => ['numeric','min:0'],
            'programacion_id' => ['exists:programaciones,id'],
        ];

        if ($this->isMethod('patch')) {
            return collect($rules)->map(fn($r)=>array_merge(['sometimes'], $r))->all();
        }

        return [
            'pernocta' => ['required','boolean'],
            'distancias_mayor_70km' => ['required','boolean'],
            'fuera_cordoba' => ['required','boolean'],
            'valor_por_docente' => ['required','numeric','min:0'],
            'valor_por_estudiante' => ['required','numeric','min:0'],
            'valor_por_acompanante' => ['required','numeric','min:0'],
            'programacion_id' => ['required','exists:programaciones,id'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $map = [
            'distanciasMayor70km' => 'distancias_mayor_70km',
            'fueraCordoba' => 'fuera_cordoba',
            'valorPorDocente' => 'valor_por_docente',
            'valorPorEstudiante' => 'valor_por_estudiante',
            'valorPorAcompanante' => 'valor_por_acompanante',
            'programacionId' => 'programacion_id',
        ];
        
        $this->merge(collect($map)->mapWithKeys(fn ($out, $in) => [$out => $this->input($in)])->filter(fn ($v) => !is_null($v))->all());
    }
}
