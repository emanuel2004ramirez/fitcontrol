<?php

namespace App\Support\Auditing;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use JsonException;

final class AuditTrail
{
    /**
     * Registra un evento exclusivamente mediante el procedimiento de auditoria.
     *
     * @throws JsonException
     */
    public function record(
        string $event,
        string $entity,
        string|int|null $entityId = null,
        ?array $before = null,
        ?array $after = null,
        ?string $reason = null,
        ?int $userId = null,
    ): void {
        $request = app()->bound('request') ? request() : null;

        DB::statement('CALL sp_auditoria_registrar(?, ?, ?, ?, ?, ?, ?, ?, ?)', [
            $userId ?? Auth::id(),
            Str::upper($event),
            Str::limit($entity, 120, ''),
            $entityId === null ? null : Str::limit((string) $entityId, 64, ''),
            $before === null ? null : json_encode($before, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
            $after === null ? null : json_encode($after, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
            $reason === null ? null : Str::limit($reason, 255, ''),
            $request?->ip(),
            $request ? Str::limit((string) $request->userAgent(), 2000, '') : null,
        ]);
    }

    public function recordProcedure(string $procedure, array $parameters, mixed $result = null): void
    {
        $metadata = ProcedureAuditMetadata::from($procedure, $parameters, $result);

        if ($metadata === null) {
            return;
        }

        $this->record(
            $metadata->event,
            $metadata->entity,
            $metadata->entityId,
            after: [
                'procedimiento' => $procedure,
                'parametros' => $metadata->parameters,
            ],
            reason: $metadata->reason,
        );
    }
}
