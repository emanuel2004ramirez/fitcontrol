<?php

namespace App\Support\Reports;

use InvalidArgumentException;

final class ReportDefinition
{
    private const REPORTS = [
        'clientes' => ['title' => 'Clientes', 'icon' => 'bi-people', 'columns' => ['numero_socio' => 'Socio', 'nombre' => 'Nombre', 'apellido' => 'Apellido', 'estado' => 'Estado', 'telefono' => 'Teléfono', 'correo_electronico' => 'Correo', 'fecha_registro' => 'Registro']],
        'personal' => ['title' => 'Personal', 'icon' => 'bi-person-badge', 'columns' => ['codigo_empleado' => 'Código', 'nombre' => 'Nombre', 'apellido' => 'Apellido', 'cargo' => 'Cargo', 'estado' => 'Estado', 'telefono' => 'Teléfono', 'correo_electronico' => 'Correo', 'fecha_contratacion' => 'Contratación']],
        'pagos' => ['title' => 'Pagos', 'icon' => 'bi-wallet2', 'columns' => ['numero_recibo' => 'Recibo', 'cliente' => 'Cliente', 'metodo' => 'Método', 'estado' => 'Estado', 'monto' => 'Monto', 'moneda' => 'Moneda', 'referencia' => 'Referencia', 'pagado_at' => 'Fecha']],
        'cobros' => ['title' => 'Cobros', 'icon' => 'bi-receipt', 'columns' => ['numero_cargo' => 'Cargo', 'cliente' => 'Cliente', 'estado' => 'Estado', 'concepto' => 'Concepto', 'total' => 'Total', 'pagado' => 'Pagado', 'saldo' => 'Saldo', 'moneda' => 'Moneda', 'fecha_emision' => 'Emisión', 'fecha_vencimiento' => 'Vencimiento']],
        'asistencias' => ['title' => 'Asistencias', 'icon' => 'bi-person-check', 'columns' => ['numero_socio' => 'Socio', 'cliente' => 'Cliente', 'entrada_at' => 'Entrada', 'salida_at' => 'Salida', 'duracion_minutos' => 'Duración (min)', 'metodo_registro' => 'Método']],
        'membresias' => ['title' => 'Membresías', 'icon' => 'bi-card-checklist', 'columns' => ['numero_socio' => 'Socio', 'cliente' => 'Cliente', 'tipo' => 'Tipo', 'estado' => 'Estado', 'fecha_inicio' => 'Inicio', 'fecha_fin' => 'Fin', 'precio_contratado' => 'Precio', 'moneda' => 'Moneda', 'origen' => 'Origen']],
        'evaluaciones' => ['title' => 'Evaluaciones', 'icon' => 'bi-activity', 'columns' => ['numero_socio' => 'Socio', 'cliente' => 'Cliente', 'evaluador' => 'Evaluador', 'evaluada_at' => 'Fecha', 'metodo' => 'Método', 'medidas' => 'Medidas', 'observaciones' => 'Observaciones']],
        'entrenamientos' => ['title' => 'Entrenamientos', 'icon' => 'bi-heart-pulse', 'columns' => ['numero_socio' => 'Socio', 'cliente' => 'Cliente', 'rutina' => 'Rutina', 'sesion' => 'Sesión', 'iniciado_at' => 'Inicio', 'finalizado_at' => 'Fin', 'duracion_minutos' => 'Duración (min)', 'observaciones' => 'Observaciones']],
    ];

    public static function all(): array
    {
        return self::REPORTS;
    }

    public static function get(string $type): array
    {
        return self::REPORTS[$type] ?? throw new InvalidArgumentException('Reporte no permitido.');
    }

    public static function allowed(string $type): bool
    {
        return isset(self::REPORTS[$type]);
    }
}
