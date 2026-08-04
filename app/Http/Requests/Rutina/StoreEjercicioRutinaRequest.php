<?php

namespace App\Http\Requests\Rutina;

use App\Http\Requests\FitControlRequest;

class StoreEjercicioRutinaRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['sesion_rutina_id' => ['required', 'integer', 'min:1'], 'ejercicio_id' => ['required', 'integer', 'min:1'], 'orden' => ['required', 'integer', 'min:1'], 'series' => ['nullable', 'integer', 'min:1'], 'repeticiones_min' => ['nullable', 'integer', 'min:1'], 'repeticiones_max' => ['nullable', 'integer', 'gte:repeticiones_min'], 'duracion_segundos' => ['nullable', 'integer', 'min:1'], 'distancia' => ['nullable', 'numeric', 'min:0'], 'peso' => ['nullable', 'numeric', 'min:0'], 'descanso_segundos' => ['nullable', 'integer', 'min:0'], 'rpe' => ['nullable', 'numeric', 'between:0,10'], 'rir' => ['nullable', 'integer', 'between:0,10'], 'tempo' => ['nullable', 'string', 'max:20'], 'indicaciones' => ['nullable', 'string', 'max:5000']];
    }
}
