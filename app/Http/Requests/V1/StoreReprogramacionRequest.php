<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReprogramacionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'fecha_reprogramacion' => ['required','date'],
            'estado_reprogramacion' => ['nullable', Rule::in(['aprobada','rechazada','pendiente'])],
            'tipo_reprogramacion' => ['nullable', Rule::in(['normal','emergencia'])],
            'estado_vice' => ['nullable', Rule::in(['aprobada','rechazada','pendiente'])],
            'justificacion' => ['required','string'],
            'programacion_id' => ['sometimes','exists:programaciones,id'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $map = [
            'fechaReprogramacion' => 'fecha_reprogramacion',
            'estadoReprogramacion' => 'estado_reprogramacion',
            'tipoReprogramacion' => 'tipo_reprogramacion',
            'estadoVice' => 'estado_vice',
            'programacionId' => 'programacion_id',
        ];
        
        $this->merge(collect($map)->mapWithKeys(fn ($out, $in) => [$out => $this->input($in)])->filter(fn ($v) => !is_null($v))->all());
    }
}
