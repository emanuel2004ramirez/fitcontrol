<?php

namespace App\Http\Requests\Catalogo;

use App\Http\Requests\FitControlRequest;

class StoreCatalogoRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['codigo' => ['required', 'string', 'max:100', 'regex:/^[A-Z0-9_\-]+$/'], 'nombre' => ['required', 'string', 'max:120'], 'descripcion' => ['nullable', 'string', 'max:5000'], 'modulo' => ['nullable', 'string', 'max:60'], 'activo' => ['sometimes', 'boolean'], 'es_terminal' => ['sometimes', 'boolean'], 'permite_acceso' => ['sometimes', 'boolean'], 'requiere_referencia' => ['sometimes', 'boolean'], 'orden' => ['sometimes', 'integer', 'min:0'], 'duracion_dias' => ['nullable', 'integer', 'min:1', 'max:3650'], 'unidad' => ['nullable', 'string', 'max:20'], 'valor_minimo' => ['nullable', 'numeric'], 'valor_maximo' => ['nullable', 'numeric', 'gte:valor_minimo'], 'decimales' => ['nullable', 'integer', 'between:0,4']];
    }

    public function messages(): array
    {
        return parent::messages() + ['codigo.regex' => 'El código solo puede contener letras mayúsculas, números, guiones y guiones bajos.'];
    }
}
