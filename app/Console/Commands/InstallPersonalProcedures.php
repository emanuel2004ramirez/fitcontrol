<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InstallPersonalProcedures extends Command
{
    protected $signature = 'fitcontrol:install-personal-procedures';

    protected $description = 'Instala o actualiza los procedimientos almacenados del módulo Personal';

    public function handle(): int
    {
        $path = database_path('personal_module_stored_procedures.sql');
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

        $verification = DB::selectOne('CALL sp_personal_contar(?, ?, ?, ?, ?)', [null, null, null, null, null]);
        $this->components->info("Procedimientos de Personal instalados correctamente. Registros activos: {$verification->total}.");

        return self::SUCCESS;
    }
}
