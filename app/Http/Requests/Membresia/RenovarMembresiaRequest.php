<?php

namespace App\Http\Requests\Membresia;

use App\Http\Requests\FitControlRequest;

class RenovarMembresiaRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['tipo_membresia_id' => ['required', 'integer', 'min:1'], 'precio_membresia_id' => ['required', 'integer', 'min:1'], 'estado_membresia_id' => ['required', 'integer', 'min:1'], 'fecha_inicio' => ['required', 'date'], 'fecha_fin' => ['required', 'date', 'after_or_equal:fecha_inicio']];
    }
}
