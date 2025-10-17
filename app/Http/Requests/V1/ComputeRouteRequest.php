<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class ComputeRouteRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'origin.lat' => ['required','numeric','between:-90,90'],
            'origin.lng' => ['required','numeric','between:-180,180'],
            'dest.lat'   => ['required','numeric','between:-90,90'],
            'dest.lng'   => ['required','numeric','between:-180,180'],
            'mode'       => ['sometimes','in:DRIVE,BICYCLE,WALK,TRANSIT,TWO_WHEELER'],
        ];
    }
}
