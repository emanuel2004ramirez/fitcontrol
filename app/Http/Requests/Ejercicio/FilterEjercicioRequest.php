<?php

namespace App\Http\Requests\Ejercicio;

use App\Http\Requests\FitControlRequest;

class FilterEjercicioRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['texto' => ['nullable', 'string', 'max:150'], 'estado_id' => ['nullable', 'integer', 'min:1'], 'grupo_id' => ['nullable', 'integer', 'min:1'], 'patron' => ['nullable', 'string', 'max:80'], 'equipamiento' => ['nullable', 'string', 'max:120'], 'por_pagina' => ['nullable', 'integer', 'in:10,15,25,50,100'], 'page' => ['nullable', 'integer', 'min:1']];
    }
}
