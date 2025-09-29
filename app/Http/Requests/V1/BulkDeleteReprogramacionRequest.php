<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class BulkDeleteReprogramacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Ajusta a tu política/rol
        return $this->user()?->can('delete', \App\Models\Reprogramacion::class) ?? true;
    }

    public function rules(): array
    {
        return [
            'ids'   => ['required','array','min:1','max:1000'], // limita tamaño del lote
            'ids.*' => ['integer','distinct','min:1'],
            // opcional: 'force' => ['boolean'], // para forceDelete si usas SoftDeletes
        ];
    }

    public function messages(): array
    {
        return [
            'ids.required' => 'Debes enviar al menos un id.',
        ];
    }
}