<?php

namespace App\Http\Requests\Usuario;

use App\Http\Requests\FitControlRequest;

class CambiarPasswordRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['password' => ['required', 'string', 'min:6', 'max:72', 'confirmed'], 'debe_cambiar_password' => ['sometimes', 'boolean']];
    }
}
