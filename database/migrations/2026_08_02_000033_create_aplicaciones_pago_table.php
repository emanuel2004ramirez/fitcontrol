<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aplicaciones_pago', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pago_id')->constrained('pagos')->restrictOnDelete();
            $table->foreignId('cargo_cobro_id')->constrained('cargos_cobro')->restrictOnDelete();
            $table->decimal('monto_aplicado', 12, 2);
            $table->timestamps();
            $table->unique(['pago_id', 'cargo_cobro_id'], 'uq_aplicacion_pago_cargo');
            $table->index('cargo_cobro_id');
        });
        DB::statement('ALTER TABLE aplicaciones_pago ADD CONSTRAINT chk_aplicacion_monto CHECK (monto_aplicado > 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('aplicaciones_pago');
    }
};
