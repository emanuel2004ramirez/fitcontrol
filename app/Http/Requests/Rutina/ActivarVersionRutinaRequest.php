<?php

namespace App\Http\Requests\Rutina;

use App\Http\Requests\FitControlRequest;

class ActivarVersionRutinaRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['version_rutina_id' => ['required', 'integer', 'min:1'], 'motivo' => ['required', 'string', 'max:255']];
    }
}
