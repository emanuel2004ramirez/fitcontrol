<?php

namespace App\Http\Requests\Asistencia;

use App\Http\Requests\FitControlRequest;

class RegistrarSalidaRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['salida_at' => ['nullable', 'date'], 'usuario_id' => ['nullable', 'integer', 'min:1']];
    }
}
