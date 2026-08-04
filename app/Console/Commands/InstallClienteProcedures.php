<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InstallClienteProcedures extends Command
{
    protected $signature = 'fitcontrol:install-clientes-procedures';

    protected $description = 'Instala o actualiza los procedimientos almacenados del módulo Clientes';

    public function handle(): int
    {
        $path = database_path('clientes_module_stored_procedures.sql');
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
        $total = DB::selectOne('CALL sp_clientes_contar(?, ?, ?, ?, ?)', [null, null, null, null, null])?->total ?? 0;
        $this->components->info("Procedimientos de Clientes instalados correctamente. Registros activos: {$total}.");

        return self::SUCCESS;
    }
}
