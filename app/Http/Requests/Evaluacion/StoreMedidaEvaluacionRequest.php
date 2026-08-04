<?php

namespace App\Http\Requests\Evaluacion;

use App\Http\Requests\FitControlRequest;

class StoreMedidaEvaluacionRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['evaluacion_fisica_id' => ['required', 'integer', 'min:1'], 'tipo_medida_id' => ['required', 'integer', 'min:1'], 'valor' => ['required', 'numeric', 'between:-99999999,99999999'], 'instrumento' => ['nullable', 'string', 'max:100'], 'observaciones' => ['nullable', 'string', 'max:5000']];
    }
}
