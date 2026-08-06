<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClienteBienvenidaMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly object $cliente) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Bienvenido a FitControl');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.clientes.bienvenida');
    }

    public function attachments(): array
    {
        return [];
    }
}
