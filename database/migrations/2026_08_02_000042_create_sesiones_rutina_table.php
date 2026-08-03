<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sesiones_rutina', function (Blueprint $table) {
            $table->id();
            $table->foreignId('version_rutina_id')->constrained('versiones_rutina')->restrictOnDelete();
            $table->unsignedSmallInteger('numero_sesion');
            $table->string('nombre', 100);
            $table->unsignedTinyInteger('dia_semana')->nullable();
            $table->text('indicaciones')->nullable();
            $table->timestamps();
            $table->unique(['version_rutina_id', 'numero_sesion'], 'uq_version_sesion');
        });
        DB::statement('ALTER TABLE sesiones_rutina ADD CONSTRAINT chk_sesion_dia CHECK (dia_semana IS NULL OR dia_semana BETWEEN 1 AND 7)');
    }

    public function down(): void
    {
        Schema::dropIfExists('sesiones_rutina');
    }
};
