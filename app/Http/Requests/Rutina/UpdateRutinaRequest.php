<?php

namespace App\Http\Requests\Rutina;

use App\Http\Requests\FitControlRequest;

class UpdateRutinaRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['estado_rutina_id' => ['required', 'integer', 'min:1'], 'nombre' => ['required', 'string', 'max:120'], 'descripcion' => ['nullable', 'string', 'max:5000'], 'fecha_inicio' => ['required', 'date'], 'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio']];
    }
}
