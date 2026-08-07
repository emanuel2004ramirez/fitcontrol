<?php
namespace App\Http\Requests\Membresia;
use App\Http\Requests\FitControlRequest;
class CancelarMembresiaRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['categoria_id'=>['required','integer','min:1'],'fecha_efectiva'=>['required','date'],'motivo'=>['required','string','max:255'],'observaciones'=>['nullable','string','max:5000']];
    }
}
