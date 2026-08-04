<?php

namespace App\Http\Requests\Pago;

use App\Http\Requests\FitControlRequest;

class AplicarPagoRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['pago_id' => ['required', 'integer', 'min:1'], 'cargo_id' => ['required', 'integer', 'min:1'], 'monto' => ['required', 'numeric', 'gt:0', 'decimal:0,2']];
    }
}
