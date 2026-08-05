<?php

namespace App\Support\Auditing;

use Illuminate\Support\Str;

final readonly class ProcedureAuditMetadata
{
    private const ACTIONS = [
        'cambiar_estado' => 'CAMBIAR_ESTADO',
        'actualizar' => 'EDITAR',
        'guardar' => 'EDITAR',
        'eliminar' => 'ELIMINAR',
        'retirar' => 'ELIMINAR',
        'cancelar' => 'CAMBIAR_ESTADO',
        'reactivar' => 'CAMBIAR_ESTADO',
        'activar' => 'CAMBIAR_ESTADO',
        'publicar' => 'CAMBIAR_ESTADO',
        'congelar' => 'CAMBIAR_ESTADO',
        'registrar_salida' => 'OPERACION_IMPORTANTE',
        'crear' => 'CREAR',
        'registrar' => 'CREAR',
        'agregar' => 'OPERACION_IMPORTANTE',
        'iniciar' => 'OPERACION_IMPORTANTE',
        'asignar' => 'OPERACION_IMPORTANTE',
        'aplicar' => 'OPERACION_IMPORTANTE',
        'renovar' => 'OPERACION_IMPORTANTE',
        'duplicar' => 'OPERACION_IMPORTANTE',
        'reembolsar' => 'OPERACION_IMPORTANTE',
        'finalizar' => 'OPERACION_IMPORTANTE',
    ];

    public function __construct(
        public string $event,
        public string $entity,
        public string|int|null $entityId,
        public array $parameters,
        public ?string $reason,
    ) {}

    public static function from(string $procedure, array $parameters, mixed $result): ?self
    {
        if ($procedure === 'sp_auditoria_registrar') {
            return null;
        }

        if ($procedure === 'sp_reportes_generar') {
            return new self('CONSULTAR_REPORTE', 'reportes', $parameters[0] ?? null, self::sanitize($procedure, $parameters), null);
        }

        $matchedAction = null;
        $event = null;
        foreach (self::ACTIONS as $action => $candidate) {
            if (str_contains($procedure, "_{$action}")) {
                $matchedAction = $action;
                $event = $candidate;
                break;
            }
        }

        if ($event === null) {
            return null;
        }

        $entity = Str::after($procedure, 'sp_');
        $entity = Str::before($entity, "_{$matchedAction}");
        $entityId = self::resultId($result) ?? self::inferId($event, $parameters);

        return new self($event, $entity, $entityId, self::sanitize($procedure, $parameters), self::inferReason($parameters));
    }

    private static function inferId(string $event, array $parameters): string|int|null
    {
        if ($event === 'CREAR' || $parameters === []) {
            return null;
        }

        return is_int($parameters[0] ?? null) || is_string($parameters[0] ?? null) ? $parameters[0] : null;
    }

    private static function resultId(mixed $result): string|int|null
    {
        if (! is_object($result)) {
            return null;
        }

        foreach (get_object_vars($result) as $key => $value) {
            if (($key === 'id' || str_ends_with($key, '_id')) && (is_int($value) || is_string($value))) {
                return $value;
            }
        }

        return null;
    }

    private static function inferReason(array $parameters): ?string
    {
        foreach (array_reverse($parameters) as $parameter) {
            if (is_string($parameter) && mb_strlen($parameter) >= 5 && mb_strlen($parameter) <= 255) {
                return $parameter;
            }
        }

        return null;
    }

    private static function sanitize(string $procedure, array $parameters): array
    {
        if (str_contains($procedure, 'password')) {
            return array_map(fn (mixed $value, int $index): mixed => $index === 0 ? $value : '[PROTEGIDO]', $parameters, array_keys($parameters));
        }

        return array_map(function (mixed $value): mixed {
            if (is_string($value) && mb_strlen($value) > 1000) {
                return mb_substr($value, 0, 1000).'…';
            }

            return $value;
        }, $parameters);
    }
}
