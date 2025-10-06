<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BulkCatalogoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items'                       => ['required','array','min:1','max:1000'],
            'items.*'                     => ['required','array'],
            'items.*.nivel_academico'     => ['required', Rule::in(['pregrado','postgrado'])],
            'items.*.facultad'            => ['required','string','max:255'],
            'items.*.programa_academico'  => ['required','string','max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Debes enviar al menos un elemento.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            $items = collect($this->input('items', []));

            $norm = function (?string $s): string {
                $s = (string) $s;
                $s = preg_replace('/\s+/u', ' ', trim($s)); 
                return mb_strtolower($s);                   
            };

            $dupes = $items->groupBy(function ($i) use ($norm) {
                $fac = $norm($i['facultad'] ?? '');
                $pro = $norm($i['programa_academico'] ?? '');
                return $fac.'|'.$pro;
            })->filter(fn($g) => $g->count() > 1)->keys();

            if ($dupes->isNotEmpty()) {
                $v->errors()->add('items', 'Hay combinaciones repetidas (facultad, programa_académico) en el payload.');
            }
        });
    }
}
