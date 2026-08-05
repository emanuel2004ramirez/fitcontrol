<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InstallEvaluacionProcedures extends Command
{
    protected $signature = 'fitcontrol:install-evaluaciones-procedures';

    protected $description = 'Instala procedimientos de evaluaciones físicas';

    public function handle(): int
    {
        $p = database_path('evaluaciones_module_stored_procedures.sql');
        $s = file_get_contents($p);
        if ($s === false) {
            throw new RuntimeException("No se pudo leer {$p}");
        }$s = preg_replace('/^DELIMITER.*$/m', '', $s) ?? $s;
        foreach (preg_split('/\$\$\s*/', $s) ?: [] as $q) {
            if (($q = trim($q)) !== '') {
                DB::unprepared($q);
            }
        }$r = DB::selectOne('CALL sp_evaluaciones_contar(?, ?, ?, ?, ?)', [null, null, null, null, null]);
        $this->components->info("Procedimientos instalados. Evaluaciones: {$r->total}.");

        return self::SUCCESS;
    }
}
