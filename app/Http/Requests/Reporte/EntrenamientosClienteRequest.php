<?php

namespace App\Http\Requests\Reporte;

use App\Http\Requests\FitControlRequest;

class EntrenamientosClienteRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['cliente_id' => ['required', 'integer', 'min:1'], 'desde' => ['required', 'date'], 'hasta' => ['required', 'date', 'after_or_equal:desde']];
    }
}
