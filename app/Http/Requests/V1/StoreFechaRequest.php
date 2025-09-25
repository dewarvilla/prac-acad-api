<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\PeriodoFechasRule;

class StoreFechaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'periodo' => [
                'required',
                'string',
                'regex:/^\d{4}-(1|2)$/',  // ej: 2025-1 o 2025-2
                'unique:fechas,periodo',
            ],

            'fecha_apertura_preg'            => ['required','date_format:Y-m-d'],
            'fecha_cierre_docente_preg'      => ['required','date_format:Y-m-d','after_or_equal:fecha_apertura_preg'],
            'fecha_cierre_jefe_depart'       => ['required','date_format:Y-m-d','after_or_equal:fecha_cierre_docente_preg'],
            'fecha_cierre_decano'            => ['required','date_format:Y-m-d','after_or_equal:fecha_cierre_jefe_depart'],

            'fecha_apertura_postg'           => ['required','date_format:Y-m-d'],
            'fecha_cierre_docente_postg'     => ['required','date_format:Y-m-d','after_or_equal:fecha_apertura_postg'],
            'fecha_cierre_coordinador_postg' => ['required','date_format:Y-m-d','after_or_equal:fecha_cierre_docente_postg'],
            'fecha_cierre_jefe_postg'        => ['required','date_format:Y-m-d','after_or_equal:fecha_cierre_coordinador_postg'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            $data = $this->all();

            $rule = new PeriodoFechasRule($data['periodo'] ?? null);

            $campos = [
                'fecha_apertura_preg',
                'fecha_cierre_docente_preg',
                'fecha_cierre_jefe_depart',
                'fecha_cierre_decano',
                'fecha_apertura_postg',
                'fecha_cierre_docente_postg',
                'fecha_cierre_coordinador_postg',
                'fecha_cierre_jefe_postg',
            ];

            foreach ($campos as $campo) {
                if (!isset($data[$campo])) continue;
                if (!$rule->passes($campo, $data[$campo])) {
                    $v->errors()->add($campo, $rule->message());
                }
            }
        });
    }
}
