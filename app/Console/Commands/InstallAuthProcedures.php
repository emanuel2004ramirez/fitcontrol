<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InstallAuthProcedures extends Command
{
    protected $signature = 'fitcontrol:install-auth-procedures';

    protected $description = 'Instala los procedimientos almacenados de autenticación';

    public function handle(): int
    {
        $sql = file_get_contents(database_path('auth_module_stored_procedures.sql'));
        if ($sql === false) {
            throw new RuntimeException('No se pudo leer el archivo de procedimientos de autenticación.');
        }
        $sql = preg_replace('/^DELIMITER \$\$\R|^DELIMITER ;\R?/m', '', $sql);
        foreach (preg_split('/\$\$\s*/', (string) $sql, -1, PREG_SPLIT_NO_EMPTY) as $statement) {
            DB::unprepared(trim($statement));
        }
        $this->info('Procedimientos de autenticación instalados.');

        return self::SUCCESS;
    }
}
