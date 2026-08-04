<?php

namespace App\Http\Requests\Personal;

use App\Http\Requests\FitControlRequest;

class DeletePersonalRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['fecha_terminacion' => ['nullable', 'date', 'before_or_equal:today'], 'motivo' => ['required', 'string', 'max:255']];
    }
}
