<?php

namespace App\Http\Requests\Cliente;

use App\Http\Requests\FitControlRequest;

class FilterClienteRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['texto' => ['nullable', 'string', 'max:150'], 'estado_id' => ['nullable', 'integer', 'min:1'], 'sexo_id' => ['nullable', 'integer', 'min:1'], 'fecha_desde' => ['nullable', 'date'], 'fecha_hasta' => ['nullable', 'date', 'after_or_equal:fecha_desde'], 'por_pagina' => ['nullable', 'integer', 'in:10,15,25,50,100'], 'page' => ['nullable', 'integer', 'min:1']];
    }
}
