<?php

namespace App\Http\Requests\Personal;

use App\Http\Requests\FitControlRequest;

class UpdatePersonalRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['cargo_id' => ['required', 'integer', 'min:1'], 'sexo_id' => ['nullable', 'integer', 'min:1'], 'nombre' => ['required', 'string', 'max:100'], 'apellido' => ['required', 'string', 'max:100'], 'telefono' => ['nullable', 'string', 'max:25'], 'correo_electronico' => ['nullable', 'email', 'max:150']];
    }
}
