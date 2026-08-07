<?php

namespace App\Http\Requests\Catalogos;

use Illuminate\Foundation\Http\FormRequest;

class EstadoMembresiaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codigo' => ['required', 'string', 'max:255'],
            'nombre' => ['required', 'string', 'max:255'],
            'orden' => ['required', 'numeric', 'min:0'],
            'permite_acceso' => ['nullable', 'boolean'],
            'es_terminal' => ['nullable', 'boolean'],
        ];
    }
}
