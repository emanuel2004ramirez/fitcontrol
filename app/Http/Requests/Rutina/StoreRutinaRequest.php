<?php

namespace App\Http\Requests\Rutina;

use App\Http\Requests\FitControlRequest;

class StoreRutinaRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['cliente_id' => ['required', 'integer', 'min:1'], 'dia_semana' => ['nullable', 'integer', 'between:1,7'], 'entrenador_id' => ['nullable', 'integer', 'min:1'], 'estado_rutina_id' => ['required', 'integer', 'min:1'], 'nombre' => ['required', 'string', 'max:120'], 'descripcion' => ['nullable', 'string', 'max:5000'], 'fecha_inicio' => ['required', 'date'], 'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio']];
    }
}
