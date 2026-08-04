<?php

namespace App\Http\Requests\Membresia;

use App\Http\Requests\FitControlRequest;

class SuspenderMembresiaRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['fecha_inicio' => ['required', 'date'], 'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'], 'dias_extension' => ['sometimes', 'integer', 'min:0', 'max:365'], 'motivo' => ['required', 'string', 'max:255'], 'usuario_id' => ['nullable', 'integer', 'min:1']];
    }
}
