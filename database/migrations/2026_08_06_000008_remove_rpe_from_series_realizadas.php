<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->dropConstraintIfExists('series_realizadas', 'chk_serie_rpe');

        Schema::table('series_realizadas', function (Blueprint $table) {
            if (Schema::hasColumn('series_realizadas', 'rpe')) {
                $table->dropColumn('rpe');
            }
        });
    }

    protected function dropConstraintIfExists(string $table, string $constraint): void
    {
        $database = DB::getDatabaseName();
        $exists = DB::selectOne(
            'SELECT 1 FROM information_schema.table_constraints WHERE constraint_schema = ? AND table_name = ? AND constraint_name = ?',
            [$database, $table, $constraint]
        );

        if ($exists) {
            DB::statement("ALTER TABLE {$table} DROP CONSTRAINT {$constraint}");
        }
    }

    public function down(): void
    {
        Schema::table('series_realizadas', function (Blueprint $table) {
            $table->decimal('rpe', 3, 1)->nullable()->after('distancia');
        });
        DB::statement('ALTER TABLE series_realizadas ADD CONSTRAINT chk_serie_rpe CHECK (rpe IS NULL OR rpe BETWEEN 0 AND 10)');
    }
};
