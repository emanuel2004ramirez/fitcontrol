<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE series_realizadas DROP CONSTRAINT chk_serie_rpe');
        Schema::table('series_realizadas', function (Blueprint $table) {
            $table->dropColumn('rpe');
        });
    }

    public function down(): void
    {
        Schema::table('series_realizadas', function (Blueprint $table) {
            $table->decimal('rpe', 3, 1)->nullable()->after('distancia');
        });
        DB::statement('ALTER TABLE series_realizadas ADD CONSTRAINT chk_serie_rpe CHECK (rpe IS NULL OR rpe BETWEEN 0 AND 10)');
    }
};
