<?php
namespace App\Http\Requests\Cliente;
use App\Http\Requests\FitControlRequest;
class StoreClienteRequest extends FitControlRequest
{
    public function rules(): array
    {
        return [
            'sexo_id'=>['required','integer','min:1'],'estado_cliente_id'=>['required','integer','min:1'],'nombre'=>['required','string','max:100'],'apellido'=>['required','string','max:100'],
            'tipo_identificacion'=>['required','string','in:DNI,PASAPORTE,CARNET_RESIDENTE,OTRO'],'numero_identificacion'=>['required','string','max:60'],
            'telefono'=>['required','digits:8'],'correo_electronico'=>['required','email:rfc','max:150'],'direccion'=>['required','string','max:200'],'ciudad'=>['required','string','max:100'],'fecha_nacimiento'=>['required','date','before_or_equal:today'],
            'contacto.nombre_completo'=>['required','string','max:150'],'contacto.parentesco'=>['required','string','max:60'],'contacto.telefono'=>['required','digits:8'],
            'medico.condiciones_medicas'=>['nullable','string','max:5000'],'medico.alergias'=>['nullable','string','max:5000'],'medico.medicamentos'=>['nullable','string','max:5000'],'medico.restricciones_ejercicio'=>['nullable','string','max:5000'],'medico.contacto_medico'=>['nullable','string','max:150'],
            'consentimiento.aceptado'=>['required','accepted'],
        ];
    }

    public function messages(): array
    {
        return [...parent::messages(), 'telefono.digits' => 'El teléfono debe contener exactamente 8 números.', 'contacto.telefono.digits' => 'El teléfono del contacto debe contener exactamente 8 números.', 'consentimiento.aceptado.required' => 'Debe aceptar el consentimiento de privacidad.', 'consentimiento.aceptado.accepted' => 'Debe aceptar el consentimiento de privacidad.'];
    }
}
