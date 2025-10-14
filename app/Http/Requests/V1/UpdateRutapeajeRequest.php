<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRutapeajeRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $base = (new StoreRutapeajeRequest)->rules();
        return collect($base)->map(fn($r) => array_merge(['sometimes'], $r))->all();
    }
}