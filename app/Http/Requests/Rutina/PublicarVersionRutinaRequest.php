<?php

namespace App\Http\Requests\Rutina;

use App\Http\Requests\FitControlRequest;

class PublicarVersionRutinaRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['version_rutina_id' => ['required', 'integer', 'min:1']];
    }
}
