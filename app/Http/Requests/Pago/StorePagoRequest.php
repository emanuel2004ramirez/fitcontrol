<?php

namespace App\Http\Requests\Pago;

use App\Http\Requests\FitControlRequest;

class StorePagoRequest extends FitControlRequest
{
    public function rules(): array
    {
        return [
            'idempotency_key' => ['required', 'uuid'],
            'membresia_id' => ['required', 'integer', 'min:1'],
            'metodo_pago_id' => ['required', 'integer', 'min:1'],
            'referencia' => ['nullable', 'string', 'max:120'],
            'usuario_id' => ['nullable', 'integer', 'min:1'],
            'observaciones' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
