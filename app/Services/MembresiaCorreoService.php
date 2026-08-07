<?php
namespace App\Services;
use App\Mail\MembresiaNotificacionMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;
class MembresiaCorreoService
{
    public function enviar(string $correo,string $titulo,string $mensaje,object $membresia,?object $contrato=null): array
    {
        try{Mail::to($correo)->send(new MembresiaNotificacionMail($titulo,$mensaje,$membresia,$contrato));return [true,null];}catch(Throwable $e){Log::warning('Falló correo de membresía',['membresia_id'=>$membresia->id??$membresia->membresia_id??null,'error'=>$e->getMessage()]);return [false,$e->getMessage()];}
    }
}
