<?php

namespace App\Http\Requests\Rutina;

use App\Http\Requests\FitControlRequest;

class AsignarObjetivoRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['rutina_id' => ['required', 'integer', 'min:1'], 'objetivo_id' => ['required', 'integer', 'min:1']];
    }
}
