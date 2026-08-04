<?php

namespace App\Http\Requests\Reporte;

use App\Http\Requests\FitControlRequest;

class CuentasPorCobrarRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['cliente_id' => ['nullable', 'integer', 'min:1']];
    }
}
