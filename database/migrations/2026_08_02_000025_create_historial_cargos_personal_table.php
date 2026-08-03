<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historial_cargos_personal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personal_id')->constrained('personal')->restrictOnDelete();
            $table->foreignId('cargo_id')->constrained('cargos')->restrictOnDelete();
            $table->date('vigente_desde');
            $table->date('vigente_hasta')->nullable();
            $table->string('motivo', 255)->nullable();
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['personal_id', 'vigente_hasta']);
        });
        DB::statement('ALTER TABLE historial_cargos_personal ADD CONSTRAINT chk_historial_cargo_fechas CHECK (vigente_hasta IS NULL OR vigente_hasta >= vigente_desde)');
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_cargos_personal');
    }
};
