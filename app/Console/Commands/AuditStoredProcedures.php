<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class AuditStoredProcedures extends Command
{
    protected $signature = 'fitcontrol:audit-procedures';

    protected $description = 'Compara los procedimientos SQL del proyecto con los instalados en MySQL';

    public function handle(): int
    {
        $expected = $this->expectedProcedures();
        $installed = collect(DB::select(
            'SELECT ROUTINE_NAME AS nombre FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = DATABASE() AND ROUTINE_TYPE = ?',
            ['PROCEDURE'],
        ))->pluck('nombre')->sort()->values()->all();

        $missing = array_values(array_diff($expected, $installed));
        $extra = array_values(array_diff($installed, $expected));

        $this->table(['Estado', 'Cantidad'], [
            ['Definidos en el proyecto', count($expected)],
            ['Instalados en MySQL', count($installed)],
            ['Faltantes', count($missing)],
            ['Extras en MySQL', count($extra)],
        ]);

        if ($missing !== []) {
            $this->components->error('Procedimientos faltantes:');
            foreach ($missing as $procedure) {
                $this->line("  - {$procedure}");
            }
        }
        if ($extra !== []) {
            $this->components->warn('Procedimientos instalados sin definición SQL actual:');
            foreach ($extra as $procedure) {
                $this->line("  - {$procedure}");
            }
        }
        if ($missing === []) {
            $this->components->info('Todos los procedimientos definidos están instalados.');
        }

        return $missing === [] ? self::SUCCESS : self::FAILURE;
    }

    /** @return array<int, string> */
    private function expectedProcedures(): array
    {
        $procedures = [];
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(database_path()));
        foreach ($iterator as $file) {
            if (! $file->isFile() || strtolower($file->getExtension()) !== 'sql') {
                continue;
            }
            $sql = file_get_contents($file->getPathname()) ?: '';
            preg_match_all('/CREATE\s+PROCEDURE\s+`?([a-zA-Z0-9_]+)`?/i', $sql, $matches);
            $procedures = [...$procedures, ...$matches[1]];
        }
        $procedures = array_values(array_unique($procedures));
        sort($procedures);

        return $procedures;
    }
}
