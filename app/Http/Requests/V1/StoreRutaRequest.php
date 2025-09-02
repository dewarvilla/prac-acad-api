<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreRutaRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user != null && $user->tokenCan('create');
    }

    public function rules(): array
    {
        return [
            'latitud_salidas' => ['required','string','max:255'],
            'latitud_llegadas' => ['required','string','max:255'],
            'numero_recorridos' => ['required','integer','min:0'],
            'numero_peajes' => ['required','integer','min:0'],
            'valor_peajes' => ['required','numeric','min:0'],
            'valor_total_peajes' => ['required','numeric','min:0'],
            'distancia_trayectos_km' => ['required','numeric','min:0'],
            'distancia_total_km' => ['required','numeric','min:0'],
            'ruta_salida' => ['nullable','string','max:255'],
            'ruta_llegada' => ['nullable','string','max:255'],

            'practica_id' => ['required','exists:practicas,id'],
        ];
    }
}

