<?php

namespace App\Http\Requests\Ejercicio;

use App\Http\Requests\FitControlRequest;

class StoreEjercicioRequest extends FitControlRequest
{
    public function rules(): array
    {
        return [
            'estado_ejercicio_id' => ['required', 'integer', 'min:1'],
            'nombre' => ['required', 'string', 'max:120'],
            'patron_movimiento' => ['nullable', 'string', 'max:80'],
            'equipamiento' => ['nullable', 'string', 'max:120'],
            'descripcion' => ['nullable', 'string', 'max:5000'],
            'instrucciones' => ['nullable', 'string', 'max:10000'],
            'video_url' => ['nullable', 'url', 'max:500'],
        ];
    }
}
