<?php

namespace App\Http\Requests\Personal;

use App\Http\Requests\FitControlRequest;

class AsignarCargoPersonalRequest extends FitControlRequest
{
    public function rules(): array
    {
        return [
            'cargo_id' => ['required', 'integer', 'min:1'],
            'vigente_desde' => ['required', 'date', 'before_or_equal:today'],
            'motivo' => ['required', 'string', 'max:255'],
        ];
    }
}
