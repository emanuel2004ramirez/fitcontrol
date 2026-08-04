<?php

namespace App\Http\Requests\Ejercicio;

class UpdateEjercicioRequest extends StoreEjercicioRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        unset($rules['codigo']);

        return $rules;
    }
}
