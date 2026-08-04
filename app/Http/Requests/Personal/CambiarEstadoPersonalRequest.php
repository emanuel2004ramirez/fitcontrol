<?php

namespace App\Http\Requests\Personal;

use App\Http\Requests\FitControlRequest;

class CambiarEstadoPersonalRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['estado_personal_id' => ['required', 'integer', 'min:1'], 'motivo' => ['nullable', 'string', 'max:255'], 'usuario_id' => ['nullable', 'integer', 'min:1']];
    }
}
