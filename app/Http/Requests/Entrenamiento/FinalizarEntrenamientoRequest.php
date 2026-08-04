<?php

namespace App\Http\Requests\Entrenamiento;

use App\Http\Requests\FitControlRequest;

class FinalizarEntrenamientoRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['finalizado_at' => ['nullable', 'date', 'before_or_equal:now'], 'esfuerzo_percibido' => ['nullable', 'integer', 'between:1,10'], 'notas' => ['nullable', 'string', 'max:5000']];
    }
}
