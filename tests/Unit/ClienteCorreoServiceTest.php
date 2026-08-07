<?php

namespace Tests\Unit;

use App\Mail\ClienteBienvenidaMail;
use App\Services\ClienteCorreoService;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ClienteCorreoServiceTest extends TestCase
{
    public function test_envia_bienvenida_al_correo_del_cliente(): void
    {
        Mail::fake();
        $cliente = (object) [
            'id' => 10,
            'nombre' => 'Ana',
            'numero_socio' => 'CLI-000010',
            'correo_electronico' => 'ana@example.com',
        ];

        $enviado = app(ClienteCorreoService::class)->enviarBienvenida($cliente);

        $this->assertTrue($enviado);
        Mail::assertSent(ClienteBienvenidaMail::class, fn (ClienteBienvenidaMail $mail): bool => $mail->hasTo('ana@example.com'));
    }
}
