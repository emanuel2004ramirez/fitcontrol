<?php

namespace App\Http\Requests\Evaluacion;

use App\Http\Requests\FitControlRequest;

class CompararEvaluacionesRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['evaluacion_comparada_id' => ['required', 'integer', 'min:1']];
    }
}
