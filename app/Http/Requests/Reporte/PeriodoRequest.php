<?php

namespace App\Http\Requests\Reporte;

use App\Http\Requests\FitControlRequest;

class PeriodoRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['desde' => ['required', 'date'], 'hasta' => ['required', 'date', 'after_or_equal:desde']];
    }
}
