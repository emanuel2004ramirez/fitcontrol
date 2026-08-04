<?php

namespace App\Http\Requests\Asistencia;

use App\Http\Requests\FitControlRequest;

class RegistrarEntradaRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['cliente_id' => ['required', 'integer', 'min:1'], 'membresia_id' => ['required', 'integer', 'min:1'], 'entrada_at' => ['nullable', 'date'], 'metodo_registro' => ['sometimes', 'string', 'in:MANUAL,QR,BIOMETRICO,IMPORTACION'], 'usuario_id' => ['nullable', 'integer', 'min:1'], 'observaciones' => ['nullable', 'string', 'max:5000']];
    }
}
