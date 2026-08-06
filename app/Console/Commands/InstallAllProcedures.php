<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class InstallAllProcedures extends Command
{
    protected $signature = 'fitcontrol:install-all-procedures';

    protected $description = 'Instala todos los procedimientos de FitControl en el orden correcto';

    private const COMMANDS = [
        'fitcontrol:install-core-procedures',
        'fitcontrol:install-clientes-procedures',
        'fitcontrol:install-personal-procedures',
        'fitcontrol:install-membresias-procedures',
        'fitcontrol:install-cargos-cobro-procedures',
        'fitcontrol:install-pagos-procedures',
        'fitcontrol:install-ejercicios-procedures',
        'fitcontrol:install-rutinas-procedures',
        'fitcontrol:install-evaluaciones-procedures',
        'fitcontrol:install-asistencias-procedures',
        'fitcontrol:install-dashboard-procedures',
        'fitcontrol:install-reportes-procedures',
        'fitcontrol:install-auth-procedures',
        'fitcontrol:install-auditoria-procedures',
    ];

    public function handle(): int
    {
        foreach (self::COMMANDS as $command) {
            $this->newLine();
            $this->components->info("Ejecutando {$command}");
            if ($this->call($command) !== self::SUCCESS) {
                $this->components->error("Falló {$command}");

                return self::FAILURE;
            }
        }
        $this->newLine();
        $this->components->info('Todos los procedimientos fueron instalados correctamente.');

        return self::SUCCESS;
    }
}
