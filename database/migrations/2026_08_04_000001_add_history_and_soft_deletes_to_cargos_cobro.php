<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cargos_cobro', function (Blueprint $table) {
            $table->softDeletes()->after('updated_at');
            $table->index(['deleted_at', 'fecha_vencimiento'], 'idx_cargos_cobro_vigencia');
        });
        Schema::create('historial_estados_cargo_cobro', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cargo_cobro_id')->constrained('cargos_cobro')->restrictOnDelete();
            $table->foreignId('estado_anterior_id')->nullable()->constrained('estados_cargo_cobro')->restrictOnDelete();
            $table->foreignId('estado_nuevo_id')->constrained('estados_cargo_cobro')->restrictOnDelete();
            $table->string('motivo', 255);
            $table->foreignId('cambiado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('cambiado_at')->useCurrent();
            $table->timestamps();
            $table->index(['cargo_cobro_id', 'cambiado_at'], 'idx_historial_estado_cargo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_estados_cargo_cobro');
        Schema::table('cargos_cobro', function (Blueprint $table) {
            $table->dropIndex('idx_cargos_cobro_vigencia');
            $table->dropSoftDeletes();
        });
    }
};
