<?php

namespace App\Http\Requests\Reporte;

use App\Http\Requests\FitControlRequest;

class ProgresoClienteRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['cliente_id' => ['required', 'integer', 'min:1'], 'tipo_medida_id' => ['required', 'integer', 'min:1'], 'desde' => ['nullable', 'date'], 'hasta' => ['nullable', 'date', 'after_or_equal:desde']];
    }
}
