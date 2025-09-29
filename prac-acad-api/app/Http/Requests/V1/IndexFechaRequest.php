<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class IndexFechaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        if ($this->has('sort')) {
            $parts = array_map('trim', explode(',', (string)$this->input('sort')));
            $norm  = array_map(function ($p) {
                $desc = Str::startsWith($p, '-');
                $field = Str::snake(ltrim($p, '-'));
                return $desc ? "-{$field}" : $field;
            }, $parts);
            $this->merge(['sort' => implode(',', $norm)]);
        }
    }

    public function rules(): array
    {
        $sortable = [
            'id','-id','periodo','-periodo',
            'fecha_apertura_preg','-fecha_apertura_preg',
            'fecha_cierre_docente_preg','-fecha_cierre_docente_preg',
            'fecha_apertura_postg','-fecha_apertura_postg',
            'fecha_cierre_docente_postg','-fecha_cierre_docente_postg',
        ];

        return [
            'q'        => ['sometimes','string','max:255'],
            'per_page' => ['sometimes','integer','min:1','max:200'],
            'page'     => ['sometimes','integer','min:1'],

            'sort'     => ['sometimes', function($attr,$value,$fail) use ($sortable){
                foreach (explode(',', (string)$value) as $p) {
                    if (!in_array(trim($p), $sortable, true)) {
                        return $fail("El valor de sort '{$p}' no es permitido.");
                    }
                }
            }],

            'periodo'                        => ['sometimes'],
            'periodo.lk'                     => ['sometimes','string'],
            'fecha_apertura_preg'            => ['sometimes','date'],
            'fecha_cierre_docente_preg'      => ['sometimes','date'],
            'fecha_cierre_jefe_depart'       => ['sometimes','date'],
            'fecha_cierre_decano'            => ['sometimes','date'],
            'fecha_apertura_postg'           => ['sometimes','date'],
            'fecha_cierre_docente_postg'     => ['sometimes','date'],
            'fecha_cierre_coordinador_postg' => ['sometimes','date'],
            'fecha_cierre_jefe_postg'        => ['sometimes','date'],
        ];
    }
}
