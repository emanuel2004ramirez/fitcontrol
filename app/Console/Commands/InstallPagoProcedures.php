<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InstallPagoProcedures extends Command
{
    protected $signature = 'fitcontrol:install-pagos-procedures';

    protected $description = 'Instala procedimientos del módulo Pagos';

    public function handle(): int
    {
        $path = database_path('pagos_module_stored_procedures.sql');
        $sql = file_get_contents($path);
        if ($sql === false) {
            throw new RuntimeException("No se pudo leer {$path}");
        }$sql = preg_replace('/^DELIMITER.*$/m', '', $sql) ?? $sql;
        foreach (preg_split('/\$\$\s*/', $sql) ?: [] as $s) {
            if (($s = trim($s)) !== '') {
                DB::unprepared($s);
            }
        }$r = DB::selectOne('CALL sp_pagos_resumen()');
        $this->components->info("Procedimientos instalados. Pagos: {$r->pagos}.");

        return self::SUCCESS;
    }
}
