<?php

namespace App\Http\Requests\Usuario;

use App\Http\Requests\FitControlRequest;

class StoreUsuarioRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['personal_id' => ['nullable', 'integer', 'min:1'], 'name' => ['required', 'string', 'max:255'], 'username' => ['required', 'string', 'min:3', 'max:60', 'regex:/^[a-zA-Z0-9._-]+$/'], 'email' => ['nullable', 'email', 'max:150'], 'password' => ['required', 'string', 'min:6', 'max:72', 'confirmed'], 'rol_id' => ['required', 'integer', 'min:1'], 'debe_cambiar_password' => ['sometimes', 'boolean']];
    }

    public function messages(): array
    {
        return parent::messages() + ['username.regex' => 'El nombre de usuario solo puede contener letras, números, puntos, guiones y guiones bajos.', 'password.min' => 'La contraseña debe tener al menos 6 caracteres.', 'password.confirmed' => 'La confirmación de la contraseña no coincide.'];
    }
}
