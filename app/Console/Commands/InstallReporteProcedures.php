<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InstallReporteProcedures extends Command
{
    protected $signature = 'fitcontrol:install-reportes-procedures';

    protected $description = 'Instala procedimientos del módulo Reportes';

    public function handle(): int
    {
        $p = database_path('reportes_module_stored_procedures.sql');
        $s = file_get_contents($p);
        if ($s === false) {
            throw new RuntimeException("No se pudo leer {$p}");
        }$s = preg_replace('/^DELIMITER.*$/m', '', $s) ?? $s;
        foreach (preg_split('/\$\$\s*/', $s) ?: [] as $q) {
            if (($q = trim($q)) !== '') {
                DB::unprepared($q);
            }
        }$this->components->info('Procedimientos de Reportes instalados.');

        return self::SUCCESS;
    }
}
