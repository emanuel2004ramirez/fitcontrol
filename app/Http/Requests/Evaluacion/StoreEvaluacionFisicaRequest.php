<?php

namespace App\Http\Requests\Evaluacion;

use App\Http\Requests\FitControlRequest;

class StoreEvaluacionFisicaRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['cliente_id' => ['required', 'integer', 'min:1'], 'evaluador_id' => ['required', 'integer', 'min:1'], 'evaluada_at' => ['required', 'date', 'before_or_equal:now'], 'metodo' => ['required', 'string', 'max:100'], 'observaciones' => ['nullable', 'string', 'max:5000'], 'usuario_id' => ['nullable', 'integer', 'min:1'], 'medidas' => ['required', 'array', 'min:1'], 'medidas.*.tipo_medida_id' => ['required', 'integer', 'min:1'], 'medidas.*.valor' => ['required', 'numeric'], 'medidas.*.instrumento' => ['required', 'string', 'max:100']];
    }
}
