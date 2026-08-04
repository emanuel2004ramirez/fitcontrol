<?php

namespace App\Http\Requests\Reporte;

use App\Http\Requests\FitControlRequest;

class DiasRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['dias' => ['sometimes', 'integer', 'between:1,365']];
    }
}
