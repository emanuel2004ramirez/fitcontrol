<?php

namespace App\Http\Requests\Cliente;

use App\Http\Requests\FitControlRequest;

class StoreDatosMedicosRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['cliente_id' => ['required', 'integer', 'min:1'], 'condiciones_medicas' => ['nullable', 'string', 'max:5000'], 'alergias' => ['nullable', 'string', 'max:5000'], 'medicamentos' => ['nullable', 'string', 'max:5000'], 'restricciones_ejercicio' => ['nullable', 'string', 'max:5000'], 'contacto_medico' => ['nullable', 'string', 'max:150'], 'usuario_id' => ['nullable', 'integer', 'min:1']];
    }
}
