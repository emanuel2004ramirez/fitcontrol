<?php

namespace App\Http\Requests\Usuario;

use App\Http\Requests\FitControlRequest;

class CambiarPasswordRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['password_hash' => ['required', 'string', 'min:50', 'max:255'], 'debe_cambiar_password' => ['sometimes', 'boolean']];
    }
}
