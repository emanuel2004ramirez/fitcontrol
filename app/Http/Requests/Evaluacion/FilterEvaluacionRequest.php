<?php

namespace App\Http\Requests\Evaluacion;

use App\Http\Requests\FitControlRequest;

class FilterEvaluacionRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['texto' => ['nullable', 'string', 'max:150'], 'cliente_id' => ['nullable', 'integer', 'min:1'], 'evaluador_id' => ['nullable', 'integer', 'min:1'], 'desde' => ['nullable', 'date'], 'hasta' => ['nullable', 'date', 'after_or_equal:desde'], 'por_pagina' => ['nullable', 'integer', 'in:10,15,25,50'], 'page' => ['nullable', 'integer', 'min:1']];
    }
}
