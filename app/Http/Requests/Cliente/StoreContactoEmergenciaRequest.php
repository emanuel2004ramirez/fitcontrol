<?php

namespace App\Http\Requests\Cliente;

use App\Http\Requests\FitControlRequest;

class StoreContactoEmergenciaRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['id' => ['nullable', 'integer', 'min:1'], 'cliente_id' => ['required', 'integer', 'min:1'], 'nombre_completo' => ['required', 'string', 'max:150'], 'parentesco' => ['required', 'string', 'max:60'], 'telefono' => ['required', 'string', 'max:25'], 'es_principal' => ['sometimes', 'boolean']];
    }
}
