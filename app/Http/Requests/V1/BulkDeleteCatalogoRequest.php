<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class BulkDeleteCatalogoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('delete', \App\Models\Catalogo::class) ?? true;
    }

    public function rules(): array
    {
        return [
            'ids'   => ['required','array','min:1','max:1000'],
            'ids.*' => ['integer','distinct','min:1','exists:catalogos,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'ids.required' => 'Debes enviar al menos un id.',
        ];
    }
}
