<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personal', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_empleado', 30)->unique();
            $table->foreignId('cargo_id')->constrained('cargos')->restrictOnDelete();
            $table->foreignId('sexo_id')->nullable()->constrained('sexos')->restrictOnDelete();
            $table->foreignId('estado_personal_id')->constrained('estados_personal')->restrictOnDelete();
            $table->string('nombre', 100);
            $table->string('apellido', 100);
            $table->string('tipo_identificacion', 30)->nullable();
            $table->string('numero_identificacion', 60)->nullable();
            $table->string('telefono', 25)->nullable();
            $table->string('correo_electronico', 150)->nullable()->unique();
            $table->date('fecha_contratacion');
            $table->date('fecha_terminacion')->nullable();
            $table->string('motivo_terminacion', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['tipo_identificacion', 'numero_identificacion'], 'uq_personal_identificacion');
            $table->index(['apellido', 'nombre'], 'idx_personal_apellido_nombre');
        });
        DB::statement('ALTER TABLE personal ADD CONSTRAINT chk_personal_fechas CHECK (fecha_terminacion IS NULL OR fecha_terminacion >= fecha_contratacion)');
    }

    public function down(): void
    {
        Schema::dropIfExists('personal');
    }
};
