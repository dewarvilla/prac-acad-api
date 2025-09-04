<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexAuxilioRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'per_page' => ['sometimes','integer','min:1','max:200'],
            'page'     => ['sometimes','integer','min:1'],
            'sort'     => ['sometimes', Rule::in([
                'valorTotalAuxilio','-valorTotalAuxilio',
                'numeroTotalEstudiantes','-numeroTotalEstudiantes',
            ])],

            'pernocta'                => ['sometimes','boolean'],
            'distanciasMayor70km'     => ['sometimes','boolean'],
            'fueraCordoba'            => ['sometimes','boolean'],
            'numeroTotalEstudiantes'  => ['sometimes','integer','min:0'],
            'numeroTotalDocentes'     => ['sometimes','integer','min:0'],
            'numeroTotalAcompanantes' => ['sometimes','integer','min:0'],
            'valorPorDocente'         => ['sometimes','numeric'],
            'valorPorEstudiante'      => ['sometimes','numeric'],
            'valorPorAcompanante'     => ['sometimes','numeric'],
            'valorTotalAuxilio'       => ['sometimes','numeric'],
            'programacionId'          => ['sometimes','integer','min:1'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $map = [
            'distanciasMayor70km'     => 'distancias_mayor_70km',
            'fueraCordoba'            => 'fuera_cordoba',
            'numeroTotalEstudiantes'  => 'numero_total_estudiantes',
            'numeroTotalDocentes'     => 'numero_total_docentes',
            'numeroTotalAcompanantes' => 'numero_total_acompanantes',
            'valorPorDocente'         => 'valor_por_docente',
            'valorPorEstudiante'      => 'valor_por_estudiante',
            'valorPorAcompanante'     => 'valor_por_acompanante',
            'valorTotalAuxilio'       => 'valor_total_auxilio',
            'programacionId'          => 'programacion_id',
        ];
        $merge = [];
        foreach ($map as $in => $out) if ($this->has($in)) $merge[$out] = $this->input($in);

        foreach (['pernocta','distanciasMayor70km','fueraCordoba'] as $b) {
            if ($this->has($b)) $merge[$map[$b] ?? $b] = filter_var($this->input($b), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

        if ($merge) $this->merge($merge);
    }
}
