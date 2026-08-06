<?php
namespace App\Http\Requests\Cliente;
use App\Http\Requests\FitControlRequest;
class StoreClienteRequest extends FitControlRequest
{
    public function rules(): array
    {
        return [
            'sexo_id'=>['nullable','integer','min:1'],'estado_cliente_id'=>['required','integer','min:1'],'nombre'=>['required','string','max:100'],'apellido'=>['required','string','max:100'],
            'tipo_identificacion'=>['nullable','required_with:numero_identificacion','string','max:30'],'numero_identificacion'=>['nullable','required_with:tipo_identificacion','string','max:60'],
            'telefono'=>['nullable','string','max:25','regex:/^[0-9+()\-\s]+$/'],'correo_electronico'=>['required','email:rfc','max:150'],'direccion'=>['nullable','string','max:200'],'ciudad'=>['nullable','string','max:100'],'fecha_nacimiento'=>['nullable','date','before_or_equal:today'],
            'contacto.nombre_completo'=>['required','string','max:150'],'contacto.parentesco'=>['required','string','max:60'],'contacto.telefono'=>['required','string','max:25','regex:/^[0-9+()\-\s]+$/'],
            'medico.condiciones_medicas'=>['nullable','string','max:5000'],'medico.alergias'=>['nullable','string','max:5000'],'medico.medicamentos'=>['nullable','string','max:5000'],'medico.restricciones_ejercicio'=>['nullable','string','max:5000'],'medico.contacto_medico'=>['nullable','string','max:150'],
            'consentimiento.aceptado'=>['required','accepted'],
        ];
    }

    public function messages(): array
    {
        return [...parent::messages(), 'consentimiento.aceptado.required' => 'Debe aceptar el consentimiento de privacidad.', 'consentimiento.aceptado.accepted' => 'Debe aceptar el consentimiento de privacidad.'];
    }
}
