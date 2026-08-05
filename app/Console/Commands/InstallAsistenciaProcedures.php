<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InstallAsistenciaProcedures extends Command
{
    protected $signature = 'fitcontrol:install-asistencias-procedures';

    protected $description = 'Instala procedimientos del módulo Asistencias';

    public function handle(): int
    {
        $p = database_path('asistencias_module_stored_procedures.sql');
        $s = file_get_contents($p);
        if ($s === false) {
            throw new RuntimeException("No se pudo leer {$p}");
        }$s = preg_replace('/^DELIMITER.*$/m', '', $s) ?? $s;
        foreach (preg_split('/\$\$\s*/', $s) ?: [] as $q) {
            if (($q = trim($q)) !== '') {
                DB::unprepared($q);
            }
        }$r = DB::selectOne('CALL sp_asistencias_resumen()');
        $this->components->info("Procedimientos instalados. Dentro ahora: {$r->dentro_ahora}.");

        return self::SUCCESS;
    }
}
