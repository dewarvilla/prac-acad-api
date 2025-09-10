<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreAuxilioRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'pernocta' => ['required','boolean'],
            'distancias_mayor_70km' => ['required','boolean'],
            'fuera_cordoba' => ['required','boolean'],
            'numero_total_estudiantes' => ['nullable','integer','min:0'],
            'numero_total_docentes' => ['nullable','integer','min:0'],
            'numero_total_acompanantes' => ['nullable','integer','min:0'],
            'valor_por_docente' => ['nullable','numeric','min:0'],
            'valor_por_estudiante' => ['nullable','numeric','min:0'],
            'valor_por_acompanante' => ['nullable','numeric','min:0'],
            'valor_total_auxilio' => ['nullable','numeric','min:0'],
            'programacion_id' => ['sometimes','exists:programaciones,id'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $map = [
            'distanciasMayor70km' => 'distancias_mayor_70km',
            'fueraCordoba' => 'fuera_cordoba',
            'numeroTotalEstudiantes' => 'numero_total_estudiantes',
            'numeroTotalDocentes' => 'numero_total_docentes',
            'numeroTotalAcompanantes' => 'numero_total_acompanantes',
            'valorPorDocente' => 'valor_por_docente',
            'valorPorEstudiante' => 'valor_por_estudiante',
            'valorPorAcompanante' => 'valor_por_acompanante',
            'valorTotalAuxilio' => 'valor_total_auxilio',
            'programacionId' => 'programacion_id',
        ];
        
        $this->merge(collect($map)->mapWithKeys(fn ($out, $in) => [$out => $this->input($in)])->filter(fn ($v) => !is_null($v))->all());
    }
}
