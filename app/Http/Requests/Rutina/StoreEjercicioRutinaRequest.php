<?php

namespace App\Http\Requests\Rutina;

use App\Http\Requests\FitControlRequest;

class StoreEjercicioRutinaRequest extends FitControlRequest
{
    public function rules(): array
    {
        return [
            'ejercicio_id' => ['required', 'integer', 'min:1'],
            'series' => ['required', 'integer', 'min:1', 'max:99'],
            'repeticiones_min' => ['required', 'integer', 'min:1'],
            'repeticiones_max' => ['nullable', 'integer', 'gte:repeticiones_min'],
            'peso' => ['nullable', 'numeric', 'min:0'],
            'descanso_segundos' => ['nullable', 'integer', 'min:0'],
            'indicaciones' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
