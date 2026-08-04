<?php

namespace App\Http\Requests\Usuario;

use App\Http\Requests\FitControlRequest;

class UpdateUsuarioRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['name' => ['required', 'string', 'max:255'], 'username' => ['required', 'string', 'min:3', 'max:60', 'regex:/^[a-zA-Z0-9._-]+$/'], 'email' => ['nullable', 'email', 'max:150'], 'activo' => ['required', 'boolean']];
    }
}
