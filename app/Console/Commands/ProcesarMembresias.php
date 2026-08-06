<?php
namespace App\Console\Commands;
use App\Services\MembresiaCorreoService;
use App\Services\MembresiaService;
use Illuminate\Console\Command;
class ProcesarMembresias extends Command
{
    protected $signature='fitcontrol:procesar-membresias';
    protected $description='Notifica membresías que vencen en 3 días y vence las finalizadas';
    public function handle(MembresiaService $membresias,MembresiaCorreoService $correos): int
    {
        foreach($membresias->porVencerTresDias() as $m){[$ok,$error]=$correos->enviar($m->correo_electronico,'Tu membresía vence en 3 días',"Hola {$m->nombre}, tu plan {$m->plan} está próximo a vencer. Puedes comunicarte con recepción para renovarlo.",$m);$membresias->registrarNotificacion((int)$m->membresia_id,'VENCE_3_DIAS',$m->correo_electronico,$ok,$error);}
        $actualizadas=$membresias->vencer();$this->info("Membresías vencidas actualizadas: {$actualizadas}");return self::SUCCESS;
    }
}
