<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InstallCargoCobroProcedures extends Command
{
    protected $signature = 'fitcontrol:install-cargos-cobro-procedures';

    protected $description = 'Instala los procedimientos almacenados de Cargos por Cobrar';

    public function handle(): int
    {
        $path = database_path('cargos_cobro_module_stored_procedures.sql');
        $sql = file_get_contents($path);
        if ($sql === false) {
            throw new RuntimeException("No se pudo leer {$path}");
        } $sql = preg_replace('/^DELIMITER.*$/m', '', $sql) ?? $sql;
        foreach (preg_split('/\$\$\s*/', $sql) ?: [] as $s) {
            if (($s = trim($s)) !== '') {
                DB::unprepared($s);
            }
        } $r = DB::selectOne('CALL sp_cargos_cobro_resumen()');
        $this->components->info("Procedimientos instalados. Cargos: {$r->cargos}.");

        return self::SUCCESS;
    }
}
