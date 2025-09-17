<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreRutaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
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

    protected function prepareForValidation(): void
    {
        $map = [
            'latitudSalidas' => 'latitud_salidas',
            'latitudLlegadas' => 'latitud_llegadas',
            'numeroRecorridos' => 'numero_recorridos',
            'numeroPeajes' => 'numero_peajes',
            'valorPeajes' => 'valor_peajes',
            'distanciaTrayectosKm' => 'distancia_trayectos_km',
            'rutaSalida' => 'ruta_salida',
            'rutaLlegada' => 'ruta_llegada',
            'programacionId' => 'programacion_id',
        ];
        
        $this->merge(collect($map)->mapWithKeys(fn ($out, $in) => [$out => $this->input($in)])->filter(fn ($v) => !is_null($v))->all());
    }
}
