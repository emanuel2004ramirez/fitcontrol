<?php

namespace App\Http\Requests\CargoCobro;

use App\Http\Requests\FitControlRequest;

class DeleteCargoCobroRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['motivo' => ['required', 'string', 'max:255']];
    }
}
