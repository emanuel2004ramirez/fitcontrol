<?php

namespace App\Http\Requests\Auditoria;

use App\Http\Requests\FitControlRequest;

class FilterAuditoriaRequest extends FitControlRequest
{
    public function rules(): array
    {
        return [
            'texto' => ['nullable', 'string', 'max:255'],
            'evento' => ['nullable', 'string', 'max:40'],
            'entidad' => ['nullable', 'string', 'max:120'],
            'user_id' => ['nullable', 'integer', 'min:1'],
            'desde' => ['nullable', 'date'],
            'hasta' => ['nullable', 'date', 'after_or_equal:desde'],
            'por_pagina' => ['nullable', 'integer', 'in:15,25,50,100'],
        ];
    }

    public function messages(): array
    {
        return [
            'hasta.after_or_equal' => 'La fecha final debe ser igual o posterior a la fecha inicial.',
            'por_pagina.in' => 'La cantidad por página no es válida.',
        ];
    }
}
