<?php

namespace App\Http\Requests\Pago;

use App\Http\Requests\FitControlRequest;

class CambiarEstadoPagoRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['estado_pago_id' => ['required', 'integer', 'min:1'], 'motivo' => ['nullable', 'string', 'max:255'], 'usuario_id' => ['nullable', 'integer', 'min:1']];
    }
}
