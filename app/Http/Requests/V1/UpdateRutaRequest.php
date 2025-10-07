<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRutaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $base = (new StoreRutaRequest)->rules();
        // En update todo es opcional (sometimes) excepto la relación si decides permitir mover de programación:
        return collect($base)->map(function ($r, $key) {
            // programacion_id podría omitirse si no re-asignas:
            return $key === 'programacion_id'
                ? ['sometimes','exists:programaciones,id']
                : array_merge(['sometimes'], $r);
        })->all();
    }
}
