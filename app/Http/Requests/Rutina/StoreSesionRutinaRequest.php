<?php

namespace App\Http\Requests\Rutina;

use App\Http\Requests\FitControlRequest;

class StoreSesionRutinaRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['version_rutina_id' => ['required', 'integer', 'min:1'], 'numero_sesion' => ['required', 'integer', 'min:1', 'max:999'], 'nombre' => ['required', 'string', 'max:100'], 'dia_semana' => ['nullable', 'integer', 'between:1,7'], 'indicaciones' => ['nullable', 'string', 'max:5000']];
    }
}
