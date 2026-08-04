<?php

namespace App\Http\Requests\CargoCobro;

use App\Http\Requests\FitControlRequest;

class CambiarEstadoCargoCobroRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['estado_cargo_cobro_id' => ['required', 'integer', 'min:1'], 'motivo' => ['required', 'string', 'max:255']];
    }
}
