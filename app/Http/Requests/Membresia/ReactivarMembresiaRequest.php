<?php

namespace App\Http\Requests\Membresia;

use App\Http\Requests\FitControlRequest;

class ReactivarMembresiaRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['estado_membresia_id' => ['required', 'integer', 'min:1'], 'fecha_reactivacion' => ['required', 'date'], 'motivo' => ['required', 'string', 'max:255']];
    }
}
