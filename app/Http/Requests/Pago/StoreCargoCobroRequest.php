<?php

namespace App\Http\Requests\Pago;

use App\Http\Requests\FitControlRequest;

class StoreCargoCobroRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['numero_cargo' => ['required', 'string', 'max:40'], 'cliente_id' => ['required', 'integer', 'min:1'], 'membresia_id' => ['nullable', 'integer', 'min:1'], 'estado_cargo_cobro_id' => ['required', 'integer', 'min:1'], 'concepto' => ['required', 'string', 'max:150'], 'descripcion' => ['nullable', 'string', 'max:5000'], 'subtotal' => ['required', 'numeric', 'min:0', 'decimal:0,2'], 'descuento' => ['sometimes', 'numeric', 'min:0', 'decimal:0,2', 'lte:subtotal'], 'impuesto' => ['sometimes', 'numeric', 'min:0', 'decimal:0,2'], 'moneda' => ['required', 'string', 'size:3', 'uppercase'], 'fecha_vencimiento' => ['nullable', 'date', 'after_or_equal:today'], 'usuario_id' => ['nullable', 'integer', 'min:1']];
    }
}
