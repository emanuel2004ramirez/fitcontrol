<?php

namespace App\Http\Requests\Rutina;

use App\Http\Requests\FitControlRequest;

class FilterRutinaRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['texto' => ['nullable', 'string', 'max:150'], 'cliente_id' => ['nullable', 'integer', 'min:1'], 'entrenador_id' => ['nullable', 'integer', 'min:1'], 'estado_id' => ['nullable', 'integer', 'min:1'], 'por_pagina' => ['nullable', 'integer', 'in:10,15,25,50'], 'page' => ['nullable', 'integer', 'min:1']];
    }
}
