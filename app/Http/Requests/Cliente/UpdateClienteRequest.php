<?php

namespace App\Http\Requests\Cliente;

class UpdateClienteRequest extends StoreClienteRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        unset($rules['numero_socio'],$rules['estado_cliente_id'],$rules['creado_por']);

        return $rules;
    }
}
