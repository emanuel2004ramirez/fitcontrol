<?php

namespace App\Http\Requests\Auditoria;

use App\Http\Requests\FitControlRequest;

class StoreAuditoriaRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['user_id' => ['nullable', 'integer', 'min:1'], 'evento' => ['required', 'string', 'max:40'], 'entidad' => ['required', 'string', 'max:120'], 'entidad_id' => ['nullable', 'string', 'max:64'], 'valores_anteriores' => ['nullable', 'array'], 'valores_nuevos' => ['nullable', 'array'], 'motivo' => ['nullable', 'string', 'max:255'], 'ip' => ['nullable', 'ip'], 'user_agent' => ['nullable', 'string', 'max:2000']];
    }
}
