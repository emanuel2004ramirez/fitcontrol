<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluaciones_desempeno_personal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personal_id')->constrained('personal')->restrictOnDelete();
            $table->foreignId('evaluador_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('aprobado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->date('periodo_inicio');
            $table->date('periodo_fin');
            $table->date('fecha_evaluacion');
            $table->unsignedTinyInteger('puntualidad');
            $table->unsignedTinyInteger('responsabilidad');
            $table->unsignedTinyInteger('atencion_cliente');
            $table->unsignedTinyInteger('trabajo_equipo');
            $table->unsignedTinyInteger('rendimiento');
            $table->decimal('promedio', 4, 2);
            $table->enum('estado', ['pendiente', 'revisada', 'aprobada'])->default('pendiente')->index();
            $table->text('comentarios')->nullable();
            $table->timestamp('aprobado_at')->nullable();
            $table->timestamps();
            $table->index(['personal_id', 'fecha_evaluacion'], 'idx_eval_desempeno_personal_fecha');
        });

        DB::statement('ALTER TABLE evaluaciones_desempeno_personal ADD CONSTRAINT chk_eval_desempeno_periodo CHECK (periodo_fin >= periodo_inicio)');
        DB::statement('ALTER TABLE evaluaciones_desempeno_personal ADD CONSTRAINT chk_eval_desempeno_puntualidad CHECK (puntualidad BETWEEN 1 AND 5)');
        DB::statement('ALTER TABLE evaluaciones_desempeno_personal ADD CONSTRAINT chk_eval_desempeno_responsabilidad CHECK (responsabilidad BETWEEN 1 AND 5)');
        DB::statement('ALTER TABLE evaluaciones_desempeno_personal ADD CONSTRAINT chk_eval_desempeno_atencion CHECK (atencion_cliente BETWEEN 1 AND 5)');
        DB::statement('ALTER TABLE evaluaciones_desempeno_personal ADD CONSTRAINT chk_eval_desempeno_equipo CHECK (trabajo_equipo BETWEEN 1 AND 5)');
        DB::statement('ALTER TABLE evaluaciones_desempeno_personal ADD CONSTRAINT chk_eval_desempeno_rendimiento CHECK (rendimiento BETWEEN 1 AND 5)');
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluaciones_desempeno_personal');
    }
};
