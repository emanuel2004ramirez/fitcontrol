<?php

namespace App\Http\Requests\Reporte;

use App\Http\Requests\FitControlRequest;

class GenerarReporteRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['desde' => ['nullable', 'date'], 'hasta' => ['nullable', 'date', 'after_or_equal:desde'], 'busqueda' => ['nullable', 'string', 'max:150'], 'estado_id' => ['nullable', 'integer', 'min:1'], 'page' => ['nullable', 'integer', 'min:1'], 'por_pagina' => ['nullable', 'integer', 'in:15,25,50,100']];
    }
}
