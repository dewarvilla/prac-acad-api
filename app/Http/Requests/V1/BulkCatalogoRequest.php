<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BulkCatalogoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'items' => ['required','array','min:1','max:1000'],
            'items.*' => ['required','array'],

            // Tus columnas (puedes enviar camelCase si ya tienes mapping global)
            'items.*.nivelAcademico'     => ['required', Rule::in(['pregrado','postgrado'])],
            'items.*.facultad'           => ['required','string','max:255'],
            'items.*.programaAcademico'  => ['required','string','max:255'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            $items = collect($this->input('items', []));

            // Duplicados dentro del payload por combinación (case-insensitive)
            $dupes = $items->groupBy(function ($i) {
                $fac = mb_strtolower((string)($i['facultad'] ?? ''));
                $pro = mb_strtolower((string)($i['programaAcademico'] ?? ''));
                return $fac.'|'.$pro;
            })->filter(fn($g)=>$g->count()>1)->keys();

            if ($dupes->isNotEmpty()) {
                $v->errors()->add(
                    'items',
                    'Hay combinaciones repetidas (facultad, programaAcademico) en el payload.'
                );
            }
        });
    }
}
