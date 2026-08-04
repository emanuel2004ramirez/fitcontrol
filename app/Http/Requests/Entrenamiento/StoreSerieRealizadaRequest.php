<?php

namespace App\Http\Requests\Entrenamiento;

use App\Http\Requests\FitControlRequest;

class StoreSerieRealizadaRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['entrenamiento_realizado_id' => ['required', 'integer', 'min:1'], 'ejercicio_rutina_id' => ['nullable', 'integer', 'min:1'], 'ejercicio_id' => ['required', 'integer', 'min:1'], 'numero_serie' => ['required', 'integer', 'min:1'], 'repeticiones' => ['nullable', 'integer', 'min:0'], 'peso' => ['nullable', 'numeric', 'min:0'], 'duracion_segundos' => ['nullable', 'integer', 'min:0'], 'distancia' => ['nullable', 'numeric', 'min:0'], 'rpe' => ['nullable', 'numeric', 'between:0,10'], 'notas' => ['nullable', 'string', 'max:5000']];
    }
}
