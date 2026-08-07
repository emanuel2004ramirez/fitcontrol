<?php
namespace App\Http\Requests\Membresia;
use App\Http\Requests\FitControlRequest;
class AgregarBeneficiarioRequest extends FitControlRequest
{
    public function rules(): array { return ['cliente_id'=>['required','integer','min:1'],'parentesco'=>['required','string','max:60']]; }
}
