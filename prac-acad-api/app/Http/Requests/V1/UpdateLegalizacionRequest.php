<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLegalizacionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $estadoTri = ['aprobada','rechazada','pendiente'];

        $rules = [
            'fecha_legalizacion' => ['date'],
            'estado_depart' => [Rule::in($estadoTri)],
            'estado_postg' => [Rule::in($estadoTri)],
            'estado_logistica' => [Rule::in($estadoTri)],
            'estado_tesoreria' => [Rule::in($estadoTri)],
            'estado_contabilidad' => [Rule::in($estadoTri)],
            'programacion_id' => ['exists:programaciones,id'],
        ];

        if ($this->isMethod('patch')) {
            return collect($rules)->map(fn($r)=>array_merge(['sometimes'], $r))->all();
        }

        return [
            'fecha_legalizacion' => ['required','date'],
            'estado_depart' => ['required', Rule::in($estadoTri)],
            'estado_postg' => ['required', Rule::in($estadoTri)],
            'estado_logistica' => ['required', Rule::in($estadoTri)],
            'estado_tesoreria' => ['required', Rule::in($estadoTri)],
            'estado_contabilidad' => ['required', Rule::in($estadoTri)],
            'programacion_id' => ['required','exists:programaciones,id'],
        ];
    }
}

