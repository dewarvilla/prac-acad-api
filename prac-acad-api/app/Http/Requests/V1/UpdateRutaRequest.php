<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRutaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $rules = [
            'latitud_salidas' => ['string','max:255'],
            'latitud_llegadas' => ['string','max:255'],
            'numero_recorridos' => ['integer','min:0'],
            'numero_peajes' => ['integer','min:0'],
            'valor_peajes' => ['numeric','min:0'],
            'distancia_trayectos_km' => ['numeric','min:0'],
            'ruta_salida' => ['nullable','string','max:255'],
            'ruta_llegada' => ['nullable','string','max:255'],
            'programacion_id' => ['exists:programaciones,id'],
        ];

        if ($this->isMethod('patch')) {
            return collect($rules)->map(fn($r)=>array_merge(['sometimes'], $r))->all();
        }

        return [
            'latitud_salidas' => ['required','string','max:255'],
            'latitud_llegadas' => ['required','string','max:255'],
            'numero_recorridos' => ['required','integer','min:0'],
            'numero_peajes' => ['required','integer','min:0'],
            'valor_peajes' => ['required','numeric','min:0'],
            'distancia_trayectos_km' => ['required','numeric','min:0'],
            'ruta_salida' => ['nullable','string','max:255'],
            'ruta_llegada' => ['nullable','string','max:255'],
            'programacion_id' => ['required','exists:programaciones,id'],
        ];
    }
}

