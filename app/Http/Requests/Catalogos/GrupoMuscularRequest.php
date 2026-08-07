<?php

namespace App\Http\Requests\Catalogos;

use Illuminate\Foundation\Http\FormRequest;

class GrupoMuscularRequest extends FormRequest
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
            'activo' => ['nullable', 'boolean'],
        ];
    }
}
