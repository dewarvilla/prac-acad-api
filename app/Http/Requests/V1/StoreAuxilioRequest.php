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
            'valor_por_docente' => ['nullable','numeric','min:0'],
            'valor_por_estudiante' => ['nullable','numeric','min:0'],
            'valor_por_acompanante' => ['nullable','numeric','min:0'],
            'programacion_id' => ['sometimes','exists:programaciones,id'],
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
