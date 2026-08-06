<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InstallCoreProcedures extends Command
{
    protected $signature = 'fitcontrol:install-core-procedures';

    protected $description = 'Instala los procedimientos base y catálogos de FitControl';

    public function handle(): int
    {
        $path = database_path('fitcontrol_stored_procedures.sql');
        $sql = file_get_contents($path);
        if ($sql === false) {
            throw new RuntimeException("No se pudo leer {$path}.");
        }
        $sql = preg_replace('/^DELIMITER.*$/m', '', $sql) ?? $sql;
        foreach (preg_split('/\$\$\s*/', $sql, -1, PREG_SPLIT_NO_EMPTY) as $statement) {
            DB::unprepared(trim($statement));
        }
        $this->info('Procedimientos base instalados correctamente.');

        return self::SUCCESS;
    }
}
