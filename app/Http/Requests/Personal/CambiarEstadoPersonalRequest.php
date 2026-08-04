<?php

namespace App\Http\Requests\Personal;

use App\Http\Requests\FitControlRequest;

class CambiarEstadoPersonalRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['estado_personal_id' => ['required', 'integer', 'min:1'], 'motivo' => ['required', 'string', 'max:255']];
    }
}
