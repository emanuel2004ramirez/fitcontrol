<?php
namespace App\Http\Requests\Membresia;
use App\Http\Requests\FitControlRequest;
class RetirarBeneficiarioRequest extends FitControlRequest
{
    public function rules(): array { return ['motivo'=>['required','string','max:255']]; }
}
