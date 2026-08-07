<?php

namespace App\Support\Reports;

use InvalidArgumentException;

final class ReportDefinition
{
    private const REPORTS = [
        'pagos' => [
            'title' => 'Ingresos y pagos', 'category' => 'management', 'icon' => 'bi-wallet2',
            'description' => 'Analiza los ingresos registrados, métodos utilizados y estados de pago.',
            'search' => 'Recibo o cliente', 'date_from' => 'Pagados desde', 'date_to' => 'Pagados hasta',
            'columns' => ['numero_recibo' => 'Recibo', 'cliente' => 'Cliente', 'metodo' => 'Método', 'estado' => 'Estado', 'monto' => 'Monto', 'moneda' => 'Moneda', 'referencia' => 'Referencia', 'pagado_at' => 'Fecha'],
            'formats' => ['monto' => 'money', 'pagado_at' => 'datetime'],
        ],
        'membresias' => [
            'title' => 'Membresías', 'category' => 'management', 'icon' => 'bi-card-checklist',
            'description' => 'Consulta vigencias, vencimientos, planes contratados y estados.',
            'search' => 'Número de socio o cliente', 'date_from' => 'Vigentes desde', 'date_to' => 'Vigentes hasta',
            'columns' => ['numero_socio' => 'Socio', 'cliente' => 'Cliente', 'tipo' => 'Plan', 'estado' => 'Estado', 'fecha_inicio' => 'Inicio', 'fecha_fin' => 'Fin', 'precio_contratado' => 'Precio', 'moneda' => 'Moneda', 'origen' => 'Origen'],
            'formats' => ['fecha_inicio' => 'date', 'fecha_fin' => 'date', 'precio_contratado' => 'money'],
        ],
        'asistencias' => [
            'title' => 'Asistencias', 'category' => 'management', 'icon' => 'bi-person-check',
            'description' => 'Mide visitas, duración de permanencia y frecuencia de clientes.',
            'search' => 'Número de socio o cliente', 'date_from' => 'Entradas desde', 'date_to' => 'Entradas hasta',
            'columns' => ['numero_socio' => 'Socio', 'cliente' => 'Cliente', 'entrada_at' => 'Entrada', 'salida_at' => 'Salida', 'duracion_minutos' => 'Duración', 'metodo_registro' => 'Registro'],
            'formats' => ['entrada_at' => 'datetime', 'salida_at' => 'datetime', 'duracion_minutos' => 'minutes'],
        ],
        'evaluaciones' => [
            'title' => 'Progreso físico', 'category' => 'management', 'icon' => 'bi-activity',
            'description' => 'Revisa la evolución de las medidas corporales sin exponer notas médicas.',
            'search' => 'Número de socio o cliente', 'date_from' => 'Evaluaciones desde', 'date_to' => 'Evaluaciones hasta',
            'columns' => ['numero_socio' => 'Socio', 'cliente' => 'Cliente', 'evaluador' => 'Evaluador', 'evaluada_at' => 'Fecha', 'medida' => 'Medida', 'valor' => 'Valor', 'unidad' => 'Unidad', 'instrumento' => 'Instrumento'],
            'formats' => ['evaluada_at' => 'datetime', 'valor' => 'decimal'],
        ],
        'clientes' => [
            'title' => 'Clientes', 'category' => 'administrative', 'icon' => 'bi-people',
            'description' => 'Directorio exportable de clientes y sus datos de contacto.',
            'search' => 'Número de socio, nombre o apellido', 'date_from' => 'Registrados desde', 'date_to' => 'Registrados hasta',
            'columns' => ['numero_socio' => 'Socio', 'nombre' => 'Nombre', 'apellido' => 'Apellido', 'estado' => 'Estado', 'telefono' => 'Teléfono', 'correo_electronico' => 'Correo', 'fecha_registro' => 'Registro'],
            'formats' => ['fecha_registro' => 'date'],
        ],
        'personal' => [
            'title' => 'Personal', 'category' => 'administrative', 'icon' => 'bi-person-badge',
            'description' => 'Directorio exportable de empleados, cargos y estados laborales.',
            'search' => 'Código, nombre o apellido', 'date_from' => 'Contratados desde', 'date_to' => 'Contratados hasta',
            'columns' => ['codigo_empleado' => 'Código', 'nombre' => 'Nombre', 'apellido' => 'Apellido', 'cargo' => 'Cargo', 'estado' => 'Estado', 'telefono' => 'Teléfono', 'correo_electronico' => 'Correo', 'fecha_contratacion' => 'Contratación'],
            'formats' => ['fecha_contratacion' => 'date'],
        ],
    ];

    public static function all(): array { return self::REPORTS; }

    public static function grouped(): array
    {
        return [
            'management' => array_filter(self::REPORTS, fn (array $report): bool => $report['category'] === 'management'),
            'administrative' => array_filter(self::REPORTS, fn (array $report): bool => $report['category'] === 'administrative'),
        ];
    }

    public static function get(string $type): array
    {
        return self::REPORTS[$type] ?? throw new InvalidArgumentException('Reporte no permitido.');
    }

    public static function allowed(string $type): bool { return isset(self::REPORTS[$type]); }
}
