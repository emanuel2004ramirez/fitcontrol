<?php

namespace App\Console\Commands;

use App\Services\AsistenciaService;
use Illuminate\Console\Command;

class RepairAttendanceDates extends Command
{
    protected $signature = 'fitcontrol:repair-attendance-dates';

    protected $description = 'Corrige entradas abiertas cuya fecha futura no coincide con su fecha de creación';

    public function handle(AsistenciaService $service): int
    {
        $corrected = $service->repararFechasFuturas();
        $this->info("Asistencias corregidas: {$corrected}.");

        return self::SUCCESS;
    }
}
