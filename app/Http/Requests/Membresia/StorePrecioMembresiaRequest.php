<?php

namespace App\Http\Requests\Membresia;

use App\Http\Requests\FitControlRequest;

class StorePrecioMembresiaRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['tipo_membresia_id' => ['required', 'integer', 'min:1'], 'precio' => ['required', 'numeric', 'gt:0', 'decimal:0,2'], 'moneda' => ['required', 'string', 'size:3', 'uppercase'], 'vigente_desde' => ['required', 'date'], 'motivo_cambio' => ['nullable', 'string', 'max:255']];
    }
}
