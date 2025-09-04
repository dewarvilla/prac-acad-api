<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexRutaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'per_page' => ['sometimes','integer','min:1','max:200'],
            'page'     => ['sometimes','integer','min:1'],
            'sort'     => ['sometimes', Rule::in([
                'numeroRecorridos','-numeroRecorridos',
                'numeroPeajes','-numeroPeajes',
                'valorTotalPeajes','-valorTotalPeajes',
                'distanciaTotalKm','-distanciaTotalKm',
            ])],

            'latitudSalidas'       => ['sometimes','string','max:255'],
            'latitudLlegadas'      => ['sometimes','string','max:255'],
            'numeroRecorridos'     => ['sometimes','integer','min:0'],
            'numeroPeajes'         => ['sometimes','integer','min:0'],
            'valorPeajes'          => ['sometimes','numeric'],
            'valorTotalPeajes'     => ['sometimes','numeric'],
            'distanciaTrayectosKm' => ['sometimes','numeric'],
            'distanciaTotalKm'     => ['sometimes','numeric'],
            'rutaSalida'           => ['sometimes','string','max:255'],
            'rutaLlegada'          => ['sometimes','string','max:255'],
            'programacionId'       => ['sometimes','integer','min:1'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $map = [
            'latitudSalidas'       => 'latitud_salidas',
            'latitudLlegadas'      => 'latitud_llegadas',
            'numeroRecorridos'     => 'numero_recorridos',
            'numeroPeajes'         => 'numero_peajes',
            'valorPeajes'          => 'valor_peajes',
            'valorTotalPeajes'     => 'valor_total_peajes',
            'distanciaTrayectosKm' => 'distancia_trayectos_km',
            'distanciaTotalKm'     => 'distancia_total_km',
            'rutaSalida'           => 'ruta_salida',
            'rutaLlegada'          => 'ruta_llegada',
            'programacionId'       => 'programacion_id',
        ];
        $merge = [];
        foreach ($map as $in => $out) if ($this->has($in)) $merge[$out] = $this->input($in);
        if ($merge) $this->merge($merge);
    }
}
