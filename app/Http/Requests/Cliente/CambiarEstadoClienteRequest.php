<?php

namespace App\Http\Requests\Cliente;

use App\Http\Requests\FitControlRequest;

class CambiarEstadoClienteRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['estado_cliente_id' => ['required', 'integer', 'min:1'], 'motivo' => ['nullable', 'string', 'max:255'], 'usuario_id' => ['nullable', 'integer', 'min:1']];
    }
}
