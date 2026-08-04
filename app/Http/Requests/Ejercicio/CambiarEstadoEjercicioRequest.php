<?php

namespace App\Http\Requests\Ejercicio;

use App\Http\Requests\FitControlRequest;

class CambiarEstadoEjercicioRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['estado_ejercicio_id' => ['required', 'integer', 'min:1']];
    }
}
