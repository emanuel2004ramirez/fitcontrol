<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->dropConstraintIfExists('ejercicios_rutina', 'chk_ejercicio_rutina_rpe');

        Schema::table('ejercicios_rutina', function (Blueprint $table) {
            $columns = ['duracion_segundos', 'distancia', 'rpe', 'rir', 'tempo'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('ejercicios_rutina', $column)) {
                    $table->dropColumn($column);
                }
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
        Schema::table('ejercicios_rutina', function (Blueprint $table) {
            $table->unsignedInteger('duracion_segundos')->nullable();
            $table->decimal('distancia', 10, 2)->nullable();
            $table->decimal('rpe', 3, 1)->nullable();
            $table->unsignedTinyInteger('rir')->nullable();
            $table->string('tempo', 20)->nullable();
        });
    }
};
