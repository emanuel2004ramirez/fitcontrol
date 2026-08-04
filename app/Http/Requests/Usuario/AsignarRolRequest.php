<?php

namespace App\Http\Requests\Usuario;

use App\Http\Requests\FitControlRequest;

class AsignarRolRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['usuario_id' => ['required', 'integer', 'min:1'], 'rol_id' => ['required', 'integer', 'min:1']];
    }
}
