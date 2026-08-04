<?php

namespace App\Http\Requests\Ejercicio;

use App\Http\Requests\FitControlRequest;

class AsignarGrupoMuscularRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['ejercicio_id' => ['required', 'integer', 'min:1'], 'grupo_muscular_id' => ['required', 'integer', 'min:1'], 'es_principal' => ['sometimes', 'boolean']];
    }
}
