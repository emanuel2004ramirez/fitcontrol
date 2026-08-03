<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ejercicios_rutina', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesion_rutina_id')->constrained('sesiones_rutina')->restrictOnDelete();
            $table->foreignId('ejercicio_id')->constrained('ejercicios')->restrictOnDelete();
            $table->unsignedSmallInteger('orden');
            $table->unsignedSmallInteger('series')->nullable();
            $table->unsignedSmallInteger('repeticiones_min')->nullable();
            $table->unsignedSmallInteger('repeticiones_max')->nullable();
            $table->unsignedInteger('duracion_segundos')->nullable();
            $table->decimal('distancia', 10, 2)->nullable();
            $table->decimal('peso', 8, 2)->nullable();
            $table->unsignedSmallInteger('descanso_segundos')->nullable();
            $table->decimal('rpe', 3, 1)->nullable();
            $table->unsignedTinyInteger('rir')->nullable();
            $table->string('tempo', 20)->nullable();
            $table->text('indicaciones')->nullable();
            $table->timestamps();
            $table->unique(['sesion_rutina_id', 'orden'], 'uq_sesion_orden');
            $table->index(['sesion_rutina_id', 'ejercicio_id']);
        });
        DB::statement('ALTER TABLE ejercicios_rutina ADD CONSTRAINT chk_ejercicio_rutina_orden CHECK (orden > 0)');
        DB::statement('ALTER TABLE ejercicios_rutina ADD CONSTRAINT chk_ejercicio_rutina_reps CHECK (repeticiones_max IS NULL OR repeticiones_min IS NULL OR repeticiones_max >= repeticiones_min)');
        DB::statement('ALTER TABLE ejercicios_rutina ADD CONSTRAINT chk_ejercicio_rutina_peso CHECK (peso IS NULL OR peso >= 0)');
        DB::statement('ALTER TABLE ejercicios_rutina ADD CONSTRAINT chk_ejercicio_rutina_rpe CHECK (rpe IS NULL OR rpe BETWEEN 0 AND 10)');
    }

    public function down(): void
    {
        Schema::dropIfExists('ejercicios_rutina');
    }
};
