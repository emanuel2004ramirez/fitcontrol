<?php

namespace App\Http\Requests\CargoCobro;

use App\Http\Requests\FitControlRequest;

class UpdateCargoCobroRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['concepto' => ['required', 'string', 'max:150'], 'descripcion' => ['nullable', 'string', 'max:5000'], 'subtotal' => ['required', 'numeric', 'min:0', 'decimal:0,2'], 'descuento' => ['nullable', 'numeric', 'min:0', 'decimal:0,2'], 'impuesto' => ['nullable', 'numeric', 'min:0', 'decimal:0,2'], 'fecha_vencimiento' => ['nullable', 'date']];
    }
}
