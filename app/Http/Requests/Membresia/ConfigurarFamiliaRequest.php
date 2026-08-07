<?php
namespace App\Http\Requests\Membresia;
use App\Http\Requests\FitControlRequest;
class ConfigurarFamiliaRequest extends FitControlRequest
{
    public function rules(): array { return ['titular_cliente_id'=>['required','integer','min:1'],'responsable_pago_cliente_id'=>['required','integer','min:1']]; }
}
