<?php
namespace App\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
class MembresiaNotificacionMail extends Mailable
{
    use Queueable,SerializesModels;
    public function __construct(public readonly string $titulo,public readonly string $mensaje,public readonly object $membresia,public readonly ?object $contrato=null) {}
    public function envelope(): Envelope { return new Envelope(subject:$this->titulo.' · FitControl'); }
    public function content(): Content { return new Content(view:'emails.membresias.notificacion'); }
    public function attachments(): array { if(!$this->contrato){return [];} return [Attachment::fromData(fn()=>Pdf::loadView('membresias.contrato-pdf',['contrato'=>$this->contrato])->output(),"contrato-{$this->contrato->numero_contrato}.pdf")->withMime('application/pdf')]; }
}
