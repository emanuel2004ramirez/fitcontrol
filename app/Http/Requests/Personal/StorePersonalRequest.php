<?php

namespace App\Http\Requests\Personal;

use App\Http\Requests\FitControlRequest;

class StorePersonalRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['cargo_id' => ['required', 'integer', 'min:1'], 'sexo_id' => ['nullable', 'integer', 'min:1'], 'estado_personal_id' => ['required', 'integer', 'min:1'], 'nombre' => ['required', 'string', 'max:100'], 'apellido' => ['required', 'string', 'max:100'], 'tipo_identificacion' => ['nullable', 'in:DNI,PASAPORTE,CARNET_RESIDENTE,OTRO'], 'numero_identificacion' => ['nullable', 'string', 'max:60', 'required_with:tipo_identificacion'], 'telefono' => ['nullable', 'string', 'max:25'], 'correo_electronico' => ['nullable', 'email', 'max:150'], 'fecha_contratacion' => ['required', 'date', 'before_or_equal:today']];
    }
}
