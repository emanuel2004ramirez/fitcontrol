<?php

namespace App\Http\Requests\Cliente;

use App\Http\Requests\FitControlRequest;

class DeleteClienteRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['motivo' => ['required', 'string', 'max:255'], 'usuario_id' => ['nullable', 'integer', 'min:1']];
    }
}
