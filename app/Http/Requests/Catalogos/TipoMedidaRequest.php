<?php

namespace App\Http\Requests\Catalogos;

use Illuminate\Foundation\Http\FormRequest;

class TipoMedidaRequest extends FormRequest
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
            'unidad' => ['required', 'string', 'max:20'],
            'valor_minimo' => ['nullable', 'numeric'],
            'valor_maximo' => ['nullable', 'numeric'],
            'decimales' => ['required', 'integer', 'min:0', 'max:4'],
            'activo' => ['nullable', 'boolean'],
        ];
    }
}
