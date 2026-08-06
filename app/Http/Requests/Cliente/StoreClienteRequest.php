<?php

namespace App\Http\Requests\Cliente;

use App\Http\Requests\FitControlRequest;

class StoreClienteRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['sexo_id' => ['nullable', 'integer', 'min:1'], 'estado_cliente_id' => ['required', 'integer', 'min:1'], 'nombre' => ['required', 'string', 'max:100'], 'apellido' => ['required', 'string', 'max:100'], 'tipo_identificacion' => ['nullable', 'required_with:numero_identificacion', 'string', 'max:30'], 'numero_identificacion' => ['nullable', 'required_with:tipo_identificacion', 'string', 'max:60'], 'telefono' => ['nullable', 'string', 'max:25', 'regex:/^[0-9+()\-\s]+$/'], 'correo_electronico' => ['required', 'email:rfc', 'max:150'], 'direccion' => ['nullable', 'string', 'max:200'], 'ciudad' => ['nullable', 'string', 'max:100'], 'pais' => ['nullable', 'string', 'size:2', 'alpha'], 'fecha_nacimiento' => ['nullable', 'date', 'before_or_equal:today'], 'creado_por' => ['nullable', 'integer', 'min:1']];
    }
}
