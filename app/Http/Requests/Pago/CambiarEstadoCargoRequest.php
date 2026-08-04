<?php

namespace App\Http\Requests\Pago;

use App\Http\Requests\FitControlRequest;

class CambiarEstadoCargoRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['estado_cargo_cobro_id' => ['required', 'integer', 'min:1']];
    }
}
