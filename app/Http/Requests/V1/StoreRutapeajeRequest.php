<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreRutapeajeRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'ruta_id'     => ['required','exists:rutas,id'],
            'nombre'      => ['required','string','max:255'],
            'lat'         => ['nullable','numeric','between:-90,90'],
            'lng'         => ['nullable','numeric','between:-180,180'],
            'distancia_m' => ['nullable','integer','min:0'],
            'orden_km'    => ['nullable','numeric','min:0'],
            'cat_i'       => ['nullable','numeric','min:0'],
            'cat_ii'      => ['nullable','numeric','min:0'],
            'cat_iii'     => ['nullable','numeric','min:0'],
            'cat_iv'      => ['nullable','numeric','min:0'],
            'cat_v'       => ['nullable','numeric','min:0'],
            'cat_vi'      => ['nullable','numeric','min:0'],
            'cat_vii'     => ['nullable','numeric','min:0'],
            'fuente'      => ['nullable','string','max:255'],
            'fecha_tarifa'=> ['nullable','date'],
        ];
    }
}