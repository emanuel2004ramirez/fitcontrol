<?php

namespace App\Http\Requests\Pago;

use App\Http\Requests\FitControlRequest;

class StorePagoRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['idempotency_key' => ['required', 'uuid'], 'numero_recibo' => ['required', 'string', 'max:40'], 'cliente_id' => ['required', 'integer', 'min:1'], 'metodo_pago_id' => ['required', 'integer', 'min:1'], 'estado_pago_id' => ['required', 'integer', 'min:1'], 'monto' => ['required', 'numeric', 'gt:0', 'decimal:0,2'], 'moneda' => ['required', 'string', 'size:3', 'uppercase'], 'referencia' => ['nullable', 'string', 'max:120'], 'referencia_externa' => ['nullable', 'string', 'max:150'], 'pagado_at' => ['nullable', 'date'], 'usuario_id' => ['nullable', 'integer', 'min:1'], 'observaciones' => ['nullable', 'string', 'max:5000']];
    }
}
