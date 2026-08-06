<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InstallEjercicioProcedures extends Command
{
    protected $signature = 'fitcontrol:install-ejercicios-procedures';

    protected $description = 'Instala procedimientos del catálogo de ejercicios';

    public function handle(): int
    {
        $path = database_path('ejercicios_module_stored_procedures.sql');
        $sql = file_get_contents($path);
        if ($sql === false) {
            throw new RuntimeException("No se pudo leer {$path}");
        }$sql = preg_replace('/^DELIMITER.*$/m', '', $sql) ?? $sql;
        foreach (preg_split('/\$\$\s*/', $sql) ?: [] as $s) {
            if (($s = trim($s)) !== '') {
                DB::unprepared($s);
            }
        }$r = DB::selectOne('CALL sp_ejercicios_contar(?, ?, ?, ?)', [null, null, null, null]);
        $this->components->info("Procedimientos instalados. Ejercicios: {$r->total}.");

        return self::SUCCESS;
    }
}

