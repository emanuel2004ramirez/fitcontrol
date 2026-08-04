<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InstallMembresiaProcedures extends Command
{
    protected $signature = 'fitcontrol:install-membresias-procedures';

    protected $description = 'Instala o actualiza los procedimientos almacenados del módulo Membresías';

    public function handle(): int
    {
        $path = database_path('membresias_module_stored_procedures.sql');
        $sql = file_get_contents($path);
        if ($sql === false) {
            throw new RuntimeException("No se pudo leer {$path}.");
        }
        $sql = preg_replace('/^DELIMITER.*$/m', '', $sql) ?? $sql;
        foreach (preg_split('/\$\$\s*/', $sql) ?: [] as $statement) {
            if (($statement = trim($statement)) !== '') {
                DB::unprepared($statement);
            }
        }
        $total = DB::selectOne('CALL sp_membresias_contar(?, ?, ?, ?, ?)', [null, null, null, null, null])?->total ?? 0;
        $this->components->info("Procedimientos de Membresías instalados correctamente. Registros: {$total}.");

        return self::SUCCESS;
    }
}
