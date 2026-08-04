<?php

namespace App\Http\Requests\Usuario;

use App\Http\Requests\FitControlRequest;

class AsignarPermisoRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['rol_id' => ['required', 'integer', 'min:1'], 'permiso_id' => ['required', 'integer', 'min:1']];
    }
}
