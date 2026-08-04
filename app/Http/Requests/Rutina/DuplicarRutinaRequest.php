<?php

namespace App\Http\Requests\Rutina;

use App\Http\Requests\FitControlRequest;

class DuplicarRutinaRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['cliente_id' => ['required', 'integer', 'min:1'], 'entrenador_id' => ['required', 'integer', 'min:1'], 'nombre' => ['required', 'string', 'max:120']];
    }
}
