<?php

namespace App\Http\Requests\Personal;

use App\Http\Requests\FitControlRequest;

class StoreHorarioPersonalRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['id' => ['nullable', 'integer', 'min:1'], 'personal_id' => ['required', 'integer', 'min:1'], 'dia_semana' => ['required', 'integer', 'between:1,7'], 'hora_inicio' => ['required', 'date_format:H:i'], 'hora_fin' => ['required', 'date_format:H:i', 'after:hora_inicio'], 'vigente_desde' => ['required', 'date'], 'vigente_hasta' => ['nullable', 'date', 'after_or_equal:vigente_desde']];
    }
}
