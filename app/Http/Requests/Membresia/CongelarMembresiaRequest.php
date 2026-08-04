<?php

namespace App\Http\Requests\Membresia;

use App\Http\Requests\FitControlRequest;

class CongelarMembresiaRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['estado_membresia_id' => ['required', 'integer', 'min:1'], 'fecha_inicio' => ['required', 'date'], 'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'], 'dias_extension' => ['nullable', 'integer', 'min:0', 'max:365'], 'motivo' => ['required', 'string', 'max:255']];
    }
}
