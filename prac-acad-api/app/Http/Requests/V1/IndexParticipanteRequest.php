<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class IndexParticipanteRequest extends FormRequest
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
            'numero_identificacion','-numero_identificacion',
            'nombre','-nombre',
        ];

        return [
            'per_page' => ['sometimes','integer','min:1','max:200'],
            'page'     => ['sometimes','integer','min:1'],
            'sort'     => ['sometimes', function($attr,$value,$fail) use ($sortable){
                foreach (explode(',', (string)$value) as $p) {
                    if (!in_array(trim($p), $sortable, true)) {
                        return $fail("El valor de sort '{$p}' no es permitido.");
                    }
                }
            }],

            'numero_identificacion' => ['sometimes','string','max:100'],
            'tipo_participante'     => ['sometimes', Rule::in(['estudiante','docente','acompanante'])],
            'discapacidad'          => ['sometimes','boolean'],
            'nombre'                => ['sometimes','string','max:255'],
            'correo_institucional'  => ['sometimes','string','max:255'],
            'telefono'              => ['sometimes','string','max:50'],
            'programa_academico'    => ['sometimes','string','max:255'],
            'facultad'              => ['sometimes','string','max:255'],
            'repitente'             => ['sometimes','boolean'],
            'programacion_id'       => ['sometimes','integer','min:1'],
        ];
    }
}
