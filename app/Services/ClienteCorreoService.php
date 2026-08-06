<?php

namespace App\Services;

use App\Mail\ClienteBienvenidaMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ClienteCorreoService
{
    public function enviarBienvenida(object $cliente): bool
    {
        if (empty($cliente->correo_electronico)) {
            return false;
        }

        try {
            Mail::to($cliente->correo_electronico)->send(new ClienteBienvenidaMail($cliente));

            return true;
        } catch (Throwable $exception) {
            Log::warning('No se pudo enviar el correo de bienvenida del cliente.', [
                'cliente_id' => $cliente->id ?? null,
                'error' => $exception->getMessage(),
            ]);

            return false;
        }
    }
}
