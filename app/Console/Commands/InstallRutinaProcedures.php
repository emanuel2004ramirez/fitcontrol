<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InstallRutinaProcedures extends Command
{
    protected $signature = 'fitcontrol:install-rutinas-procedures';

    protected $description = 'Instala procedimientos del módulo Rutinas';

    public function handle(): int
    {
        $path = database_path('rutinas_module_stored_procedures.sql');
        $sql = file_get_contents($path);
        if ($sql === false) {
            throw new RuntimeException("No se pudo leer {$path}");
        }$sql = preg_replace('/^DELIMITER.*$/m', '', $sql) ?? $sql;
        foreach (preg_split('/\$\$\s*/', $sql) ?: [] as $s) {
            if (($s = trim($s)) !== '') {
                DB::unprepared($s);
            }
        }$r = DB::selectOne('CALL sp_rutinas_contar(?, ?, ?, ?)', [null, null, null, null]);
        $this->components->info("Procedimientos instalados. Rutinas: {$r->total}.");

        return self::SUCCESS;
    }
}
