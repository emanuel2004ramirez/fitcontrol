<?php

namespace App\Http\Requests\Asistencia;

use App\Http\Requests\FitControlRequest;

class RegistrarEntradaRequest extends FitControlRequest
{
    public function rules(): array
    {
        return [
            'cliente_id' => ['required', 'integer', 'min:1'],
            'membresia_id' => ['required', 'integer', 'min:1'],
            'entrada_at' => ['nullable', 'date', 'before_or_equal:now'],
            'metodo_registro' => ['sometimes', 'string', 'in:MANUAL'],
            'usuario_id' => ['nullable', 'integer', 'min:1'],
            'observaciones' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
