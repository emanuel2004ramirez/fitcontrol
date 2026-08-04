<?php

namespace App\Http\Requests\Rutina;

use App\Http\Requests\FitControlRequest;

class StoreVersionRutinaRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['rutina_id' => ['required', 'integer', 'min:1'], 'notas_cambio' => ['nullable', 'string', 'max:5000'], 'usuario_id' => ['nullable', 'integer', 'min:1']];
    }
}
