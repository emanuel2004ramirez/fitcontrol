<?php

namespace App\Http\Requests\Entrenamiento;

use App\Http\Requests\FitControlRequest;

class IniciarEntrenamientoRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['cliente_id' => ['required', 'integer', 'min:1'], 'version_rutina_id' => ['nullable', 'integer', 'min:1'], 'sesion_rutina_id' => ['nullable', 'integer', 'min:1'], 'iniciado_at' => ['nullable', 'date', 'before_or_equal:now']];
    }
}
