<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class StoredProcedureArchitectureTest extends TestCase
{
    #[DataProvider('businessCodeDirectories')]
    public function test_business_code_does_not_query_tables_directly(string $directory): void
    {
        $violations = [];
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file) {
            if (! $file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $contents = file_get_contents($file->getPathname()) ?: '';
            $patterns = ['/DB::(?:table|raw|insert|update|delete)\s*\(/'];
            preg_match_all('~use App\\\\Models\\\\([A-Za-z0-9_]+);~', $contents, $models);
            foreach ($models[1] as $model) {
                $patterns[] = '/\b'.preg_quote($model, '/').'::(?:query|where|find|findOrFail|first|firstOrCreate|updateOrCreate|create|insert|update|delete|all|pluck)\s*\(/';
            }

            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $contents) === 1) {
                    $violations[] = $file->getPathname();
                    break;
                }
            }
        }

        self::assertSame([], $violations, "Se encontraron consultas directas:\n".implode("\n", $violations));
    }

    public static function businessCodeDirectories(): array
    {
        return [
            'controladores' => [dirname(__DIR__, 2).'/app/Http/Controllers'],
            'servicios' => [dirname(__DIR__, 2).'/app/Services'],
            'seeders' => [dirname(__DIR__, 2).'/database/seeders'],
        ];
    }
}
