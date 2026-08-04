<?php

namespace App\Http\Requests\Membresia;

use App\Http\Requests\FitControlRequest;

class CambiarEstadoMembresiaRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['estado_membresia_id' => ['required', 'integer', 'min:1'], 'mantener_activa' => ['required', 'boolean'], 'motivo' => ['nullable', 'string', 'max:255'], 'usuario_id' => ['nullable', 'integer', 'min:1']];
    }
}
