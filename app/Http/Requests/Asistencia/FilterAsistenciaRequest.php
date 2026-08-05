<?php

namespace App\Http\Requests\Asistencia;

use App\Http\Requests\FitControlRequest;

class FilterAsistenciaRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['texto' => ['nullable', 'string', 'max:150'], 'cliente_id' => ['nullable', 'integer', 'min:1'], 'metodo' => ['nullable', 'string', 'in:MANUAL,QR,BIOMETRICO,IMPORTACION'], 'solo_abiertas' => ['nullable', 'boolean'], 'desde' => ['nullable', 'date'], 'hasta' => ['nullable', 'date', 'after_or_equal:desde'], 'por_pagina' => ['nullable', 'integer', 'in:10,15,25,50,100'], 'page' => ['nullable', 'integer', 'min:1']];
    }
}
