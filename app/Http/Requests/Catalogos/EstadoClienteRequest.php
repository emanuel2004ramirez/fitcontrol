<?php

namespace App\Http\Requests\Catalogos;

use Illuminate\Foundation\Http\FormRequest;

class EstadoClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codigo' => ['required', 'string', 'max:30'],
            'nombre' => ['required', 'string', 'max:50'],
            'orden' => ['required', 'numeric', 'min:0'],
            'activo' => ['nullable', 'boolean'],
            'es_terminal' => ['nullable', 'boolean'],
        ];
    }
}
