<?php

namespace App\Http\Requests\Cliente;

use App\Http\Requests\FitControlRequest;

class UpdateClienteRequest extends FitControlRequest
{
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:100'],
            'apellido' => ['required', 'string', 'max:100'],
            'sexo_id' => ['nullable', 'integer', 'min:1'],
            'fecha_nacimiento' => ['nullable', 'date', 'before_or_equal:today'],
            'telefono' => ['nullable', 'digits:8'],
            'correo_electronico' => ['required', 'email:rfc', 'max:150'],
            'tipo_identificacion' => ['nullable', 'required_with:numero_identificacion', 'in:DNI,PASAPORTE,CARNET_RESIDENTE,OTRO'],
            'numero_identificacion' => ['nullable', 'required_with:tipo_identificacion', 'string', 'max:60'],
            'direccion' => ['nullable', 'string', 'max:200'],
            'ciudad' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [...parent::messages(), 'telefono.digits' => 'El teléfono debe contener exactamente 8 números.'];
    }
}
