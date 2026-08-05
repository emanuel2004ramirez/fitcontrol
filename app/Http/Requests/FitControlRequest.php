<?php

namespace App\Http\Requests;

use App\Support\Authorization\FitControlPermissions;
use Illuminate\Foundation\Http\FormRequest;

abstract class FitControlRequest extends FormRequest
{
    public function authorize(): bool
    {
        $permission = FitControlPermissions::forRequest(static::class);

        if ($permission === null) {
            return true;
        }

        $permissions = $this->session()->get('permissions', []);

        return in_array('*', $permissions, true) || in_array($permission, $permissions, true);
    }

    public function messages(): array
    {
        return [
            'required' => 'El campo :attribute es obligatorio.', 'required_if' => 'El campo :attribute es obligatorio.',
            'required_with' => 'El campo :attribute es obligatorio cuando se proporciona :values.',
            'string' => 'El campo :attribute debe ser texto.', 'integer' => 'El campo :attribute debe ser un número entero.',
            'numeric' => 'El campo :attribute debe ser un número.', 'boolean' => 'El campo :attribute debe ser verdadero o falso.',
            'email' => 'El campo :attribute debe contener un correo válido.', 'date' => 'El campo :attribute debe contener una fecha válida.',
            'date_format' => 'El campo :attribute no tiene el formato esperado.', 'after' => 'El campo :attribute debe ser posterior a :date.',
            'after_or_equal' => 'El campo :attribute debe ser igual o posterior a :date.', 'before_or_equal' => 'El campo :attribute debe ser igual o anterior a :date.',
            'min' => 'El campo :attribute debe ser al menos :min.', 'max' => 'El campo :attribute no debe superar :max.',
            'between' => 'El campo :attribute debe estar entre :min y :max.', 'in' => 'El valor seleccionado para :attribute no es válido.',
            'size' => 'El campo :attribute debe tener exactamente :size caracteres.',
            'gt' => 'El campo :attribute debe ser mayor que :value.', 'gte' => 'El campo :attribute debe ser mayor o igual que :value.',
            'lte' => 'El campo :attribute debe ser menor o igual que :value.', 'decimal' => 'El campo :attribute debe tener una cantidad válida de decimales.',
            'uppercase' => 'El campo :attribute debe escribirse en mayúsculas.', 'regex' => 'El formato del campo :attribute no es válido.',
            'alpha' => 'El campo :attribute solo debe contener letras.',
            'ip' => 'El campo :attribute debe contener una dirección IP válida.',
            'uuid' => 'El campo :attribute debe ser un UUID válido.', 'url' => 'El campo :attribute debe contener una URL válida.',
            'array' => 'El campo :attribute debe ser una lista válida.', 'confirmed' => 'La confirmación de :attribute no coincide.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre' => 'nombre', 'apellido' => 'apellido', 'correo_electronico' => 'correo electrónico',
            'telefono' => 'teléfono', 'fecha_nacimiento' => 'fecha de nacimiento', 'fecha_inicio' => 'fecha de inicio',
            'fecha_fin' => 'fecha de finalización', 'vigente_desde' => 'inicio de vigencia', 'vigente_hasta' => 'fin de vigencia',
            'hora_inicio' => 'hora de inicio', 'hora_fin' => 'hora de finalización', 'numero_socio' => 'número de socio',
            'numero_identificacion' => 'número de identificación', 'codigo_empleado' => 'código de empleado',
            'password_hash' => 'hash de contraseña', 'idempotency_key' => 'clave de idempotencia',
            'numero_recibo' => 'número de recibo', 'numero_cargo' => 'número de cargo',
            'numero_reembolso' => 'número de reembolso', 'monto' => 'monto', 'moneda' => 'moneda',
            'observaciones' => 'observaciones', 'motivo' => 'motivo', 'dia_semana' => 'día de la semana',
            'estado_cliente_id' => 'estado del cliente', 'estado_personal_id' => 'estado del personal',
            'estado_membresia_id' => 'estado de la membresía', 'estado_pago_id' => 'estado del pago',
            'cliente_id' => 'cliente', 'personal_id' => 'personal', 'membresia_id' => 'membresía',
            'usuario_id' => 'usuario', 'rutina_id' => 'rutina', 'ejercicio_id' => 'ejercicio',
        ];
    }
}
