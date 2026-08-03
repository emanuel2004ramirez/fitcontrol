<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('horarios_personal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personal_id')->constrained('personal')->restrictOnDelete();
            $table->unsignedTinyInteger('dia_semana');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->date('vigente_desde');
            $table->date('vigente_hasta')->nullable();
            $table->timestamps();
            $table->unique(['personal_id', 'dia_semana', 'hora_inicio', 'vigente_desde'], 'uq_horario_inicio');
            $table->index(['personal_id', 'vigente_hasta']);
        });
        DB::statement('ALTER TABLE horarios_personal ADD CONSTRAINT chk_horario_dia CHECK (dia_semana BETWEEN 1 AND 7)');
        DB::statement('ALTER TABLE horarios_personal ADD CONSTRAINT chk_horario_horas CHECK (hora_fin > hora_inicio)');
        DB::statement('ALTER TABLE horarios_personal ADD CONSTRAINT chk_horario_vigencia CHECK (vigente_hasta IS NULL OR vigente_hasta >= vigente_desde)');
    }

    public function down(): void
    {
        Schema::dropIfExists('horarios_personal');
    }
};
