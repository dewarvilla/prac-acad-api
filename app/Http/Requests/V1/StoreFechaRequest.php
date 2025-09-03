<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreAuxilioRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user != null && $user->tokenCan('create');
    }

    public function rules(): array
    {
        return [
            'fecha_apertura' => ['required', 'date'],
            'fecha_cierre' => ['required', 'date','after:fecha_apertura'],
        ];
    }
}
