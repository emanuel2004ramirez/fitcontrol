<?php

namespace App\Http\Requests\Pago;

use App\Http\Requests\FitControlRequest;

class StoreReembolsoRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['numero_reembolso' => ['required', 'string', 'max:40'], 'pago_id' => ['required', 'integer', 'min:1'], 'monto' => ['required', 'numeric', 'gt:0', 'decimal:0,2'], 'motivo' => ['required', 'string', 'max:255'], 'referencia_externa' => ['nullable', 'string', 'max:150'], 'usuario_id' => ['nullable', 'integer', 'min:1']];
    }
}
