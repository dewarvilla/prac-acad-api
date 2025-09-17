<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexProgramacionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'per_page' => ['sometimes','integer','min:1','max:200'],
            'page'     => ['sometimes','integer','min:1'],
            'sort'     => ['sometimes', Rule::in([
                'nombrePractica','-nombrePractica',
                'fechaInicio','-fechaInicio',
                'fechaFinalizacion','-fechaFinalizacion',
                'estadoPractica','-estadoPractica',
            ])],

            'nombrePractica'   => ['sometimes','string','max:255'],
            'fechaInicio'      => ['sometimes','date'],
            'fechaFinalizacion'=> ['sometimes','date','after_or_equal:fechaInicio'],
            'creacionId'       => ['sometimes','integer','min:1'],
            'requiereTransporte'=> ['sometimes','boolean'],
            'estadoPractica'   => ['sometimes', Rule::in(['en_aprobacion','aprobada','rechazada','en_ejecucion','ejecutada','en_legalizacion','legalizada'])],
        ];
    }

    protected function prepareForValidation(): void
    {
        $map = [
            'nombrePractica'    => 'nombre_practica',
            'fechaInicio'       => 'fecha_inicio',
            'fechaFinalizacion' => 'fecha_finalizacion',
            'creacionId'        => 'creacion_id',
            'requiereTransporte'=> 'requiere_transporte',
            'estadoPractica'    => 'estado_practica',
        ];
        $merge = [];
        foreach ($map as $in => $out) if ($this->has($in)) $merge[$out] = $this->input($in);

        if ($this->has('requiereTransporte')) {
            $merge['requiere_transporte'] = filter_var($this->input('requiereTransporte'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

        if ($merge) $this->merge($merge);
    }
}

