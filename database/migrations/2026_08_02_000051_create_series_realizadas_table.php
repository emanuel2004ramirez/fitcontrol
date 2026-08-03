<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('series_realizadas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entrenamiento_realizado_id')->constrained('entrenamientos_realizados')->restrictOnDelete();
            $table->foreignId('ejercicio_rutina_id')->nullable()->constrained('ejercicios_rutina')->restrictOnDelete();
            $table->foreignId('ejercicio_id')->constrained('ejercicios')->restrictOnDelete();
            $table->unsignedSmallInteger('numero_serie');
            $table->unsignedSmallInteger('repeticiones')->nullable();
            $table->decimal('peso', 8, 2)->nullable();
            $table->unsignedInteger('duracion_segundos')->nullable();
            $table->decimal('distancia', 10, 2)->nullable();
            $table->decimal('rpe', 3, 1)->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();
            $table->unique(['entrenamiento_realizado_id', 'ejercicio_id', 'numero_serie'], 'uq_entrenamiento_ejercicio_serie');
        });
        DB::statement('ALTER TABLE series_realizadas ADD CONSTRAINT chk_serie_numero CHECK (numero_serie > 0)');
        DB::statement('ALTER TABLE series_realizadas ADD CONSTRAINT chk_serie_peso CHECK (peso IS NULL OR peso >= 0)');
        DB::statement('ALTER TABLE series_realizadas ADD CONSTRAINT chk_serie_rpe CHECK (rpe IS NULL OR rpe BETWEEN 0 AND 10)');
    }

    public function down(): void
    {
        Schema::dropIfExists('series_realizadas');
    }
};
