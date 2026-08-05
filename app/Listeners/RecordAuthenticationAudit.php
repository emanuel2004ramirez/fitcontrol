<?php

namespace App\Listeners;

use App\Support\Auditing\AuditTrail;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

final class RecordAuthenticationAudit
{
    public function __construct(private readonly AuditTrail $audit) {}

    public function handle(Login|Logout|Failed $event): void
    {
        if ($event instanceof Login) {
            $this->audit->record('INICIO_SESION', 'autenticacion', $event->user->getAuthIdentifier(), after: ['guard' => $event->guard], userId: (int) $event->user->getAuthIdentifier());

            return;
        }

        if ($event instanceof Logout) {
            $this->audit->record('CIERRE_SESION', 'autenticacion', $event->user?->getAuthIdentifier(), after: ['guard' => $event->guard], userId: $event->user === null ? null : (int) $event->user->getAuthIdentifier());

            return;
        }

        $identifier = $event->credentials['email'] ?? $event->credentials['username'] ?? null;
        $this->audit->record('INICIO_SESION_FALLIDO', 'autenticacion', null, after: ['guard' => $event->guard, 'identificador' => $identifier]);
    }
}
