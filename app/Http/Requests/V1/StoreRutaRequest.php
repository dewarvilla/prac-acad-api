<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreRutaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'programacion_id'  => ['required','exists:programaciones,id'],

            'origen_lat'       => ['nullable','numeric','between:-90,90'],
            'origen_lng'       => ['nullable','numeric','between:-180,180'],
            'origen_desc'      => ['nullable','string'],
            'origen_place_id'  => ['nullable','string','max:255'],

            'destino_lat'      => ['nullable','numeric','between:-90,90'],
            'destino_lng'      => ['nullable','numeric','between:-180,180'],
            'destino_desc'     => ['nullable','string'],
            'destino_place_id' => ['nullable','string','max:255'],

            'distancia_m'      => ['nullable','integer','min:0'],
            'duracion_s'       => ['nullable','integer','min:0'],
            'polyline'         => ['nullable','string'],

            'numero_peajes'    => ['nullable','integer','min:0'],
            'valor_peajes'     => ['nullable','numeric','min:0'],

            'orden'            => ['nullable','integer','min:1'],

            // Requerido por el Escenario 6
            'justificacion'    => ['required','string','min:10'],

            'estado'           => ['nullable','boolean'],
        ];
    }
}
