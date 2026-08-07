<?php

namespace App\Http\Requests\Ejercicio;

class UpdateEjercicioRequest extends StoreEjercicioRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        $rules['estado_ejercicio_id'] = ['required', 'integer', 'min:1'];
        return $rules;
    }
}
