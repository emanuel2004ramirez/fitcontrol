<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE ejercicios_rutina DROP CONSTRAINT chk_ejercicio_rutina_rpe');
        Schema::table('ejercicios_rutina', function (Blueprint $table) {
            $table->dropColumn(['duracion_segundos', 'distancia', 'rpe', 'rir', 'tempo']);
        });
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
