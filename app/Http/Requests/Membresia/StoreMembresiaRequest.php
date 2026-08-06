<?php
namespace App\Http\Requests\Membresia;
use App\Http\Requests\FitControlRequest;
class StoreMembresiaRequest extends FitControlRequest
{
    public function rules(): array
    {
        return ['cliente_id'=>['required','integer','min:1'],'tipo_membresia_id'=>['required','integer','min:1'],'precio_membresia_id'=>['required','integer','min:1'],'fecha_inicio'=>['required','date'],'acepta_contrato'=>['required','accepted']];
    }

    public function messages(): array
    {
        return [...parent::messages(), 'acepta_contrato.required' => 'Debe aceptar el contrato de membresía.', 'acepta_contrato.accepted' => 'Debe aceptar el contrato de membresía.'];
    }
}
