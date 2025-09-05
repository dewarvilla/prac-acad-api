<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAjusteRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'fecha_ajuste' => ['required','date'],
            'estado_ajuste' => ['required', Rule::in(['aprobada','rechazada','pendiente'])],
            'estado_vice' => ['required', Rule::in(['aprobada','rechazada','pendiente'])],
            'estado_jefe_depart' => ['required', Rule::in(['aprobada','rechazada','pendiente'])],
            'estado_coordinador_postg' => ['required', Rule::in(['aprobada','rechazada','pendiente'])],
            'justificacion' => ['required','string'],
            'programacion_id' => ['required','exists:programaciones,id'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $map = [
            'fechaAjuste' => 'fecha_ajuste',
            'estadoAjuste' => 'estado_ajuste',
            'estadoVice' => 'estado_vice',
            'estadoJefeDepart' => 'estado_jefe_depart',
            'estadoCoordinadorPostg' => 'estado_coordinador_postg',
            'programacionId' => 'programacion_id',
        ];
        $this->merge(array_intersect_key($this->all(), $map) ? collect($map)->mapWithKeys(fn($v,$k)=>[$v=>$this->$k])->all() : []);
    }
}
