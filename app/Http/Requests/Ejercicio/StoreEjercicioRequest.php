<?php

namespace App\Http\Requests\Ejercicio;

use App\Http\Requests\FitControlRequest;

class StoreEjercicioRequest extends FitControlRequest
{
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:120'],
            'equipamiento_ids' => ['nullable', 'array'],
            'equipamiento_ids.*' => ['integer', 'min:1'],
            'grupo_muscular_id' => ['required', 'integer', 'min:1'],
            'descripcion' => ['nullable', 'string', 'max:5000'],
            'instrucciones' => ['nullable', 'string', 'max:10000'],
            'video_url' => ['nullable', 'url', 'max:500'],
        ];
    }
}
