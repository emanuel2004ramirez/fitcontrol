<?php

namespace App\Http\Requests\Personal;

use App\Http\Requests\FitControlRequest;

class StoreEvaluacionDesempenoPersonalRequest extends FitControlRequest
{
    public function rules(): array
    {
        return [
            'periodo_inicio' => ['required', 'date'],
            'periodo_fin' => ['required', 'date', 'after_or_equal:periodo_inicio'],
            'fecha_evaluacion' => ['required', 'date', 'before_or_equal:today'],
            'puntualidad' => ['required', 'integer', 'between:1,5'],
            'responsabilidad' => ['required', 'integer', 'between:1,5'],
            'atencion_cliente' => ['required', 'integer', 'between:1,5'],
            'trabajo_equipo' => ['required', 'integer', 'between:1,5'],
            'rendimiento' => ['required', 'integer', 'between:1,5'],
            'comentarios' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
