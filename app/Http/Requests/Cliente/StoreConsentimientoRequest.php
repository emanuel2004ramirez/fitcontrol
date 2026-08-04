<?php

namespace App\Http\Requests\Cliente;

use App\Http\Requests\FitControlRequest;

class StoreConsentimientoRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['cliente_id' => ['required', 'integer', 'min:1'], 'tipo' => ['required', 'string', 'max:60'], 'version_documento' => ['required', 'string', 'max:30'], 'aceptado' => ['required', 'boolean'], 'ip' => ['nullable', 'ip'], 'usuario_id' => ['nullable', 'integer', 'min:1']];
    }
}
