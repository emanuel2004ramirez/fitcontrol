<?php

namespace App\Http\Requests\Membresia;

use App\Http\Requests\FitControlRequest;

class StoreMembresiaRequest extends FitControlRequest
{
    public function rules(): array
    {
        return [
            'cliente_id' => ['required', 'integer', 'min:1'],
            'tipo_membresia_id' => ['required', 'integer', 'min:1'],
            'precio_membresia_id' => ['required', 'integer', 'min:1'],
            'estado_membresia_id' => ['required', 'integer', 'min:1'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'origen' => ['sometimes', 'string', 'in:NUEVA,RENOVACION,REACTIVACION,CORTESIA,PROMOCION'],
            'membresia_anterior_id' => ['nullable', 'integer', 'min:1'],
            'usuario_id' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
