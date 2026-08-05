<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InstallAuditoriaProcedures extends Command
{
    protected $signature = 'fitcontrol:install-auditoria-procedures';

    protected $description = 'Instala los procedimientos almacenados del módulo de auditoría';

    public function handle(): int
    {
        $path = database_path('auditoria_module_stored_procedures.sql');
        $sql = file_get_contents($path);
        if ($sql === false) {
            throw new RuntimeException("No se pudo leer {$path}");
        }

        $sql = preg_replace('/^DELIMITER \$\$\R|^DELIMITER ;\R?/m', '', $sql);
        $statements = preg_split('/\$\$\s*/', (string) $sql, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($statements as $statement) {
            DB::unprepared(trim($statement));
        }

        $this->info('Procedimientos de auditoría instalados correctamente.');

        return self::SUCCESS;
    }
}
