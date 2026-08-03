<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historial_estados_pago', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pago_id')->constrained('pagos')->restrictOnDelete();
            $table->foreignId('estado_anterior_id')->nullable()->constrained('estados_pago')->restrictOnDelete();
            $table->foreignId('estado_nuevo_id')->constrained('estados_pago')->restrictOnDelete();
            $table->string('motivo', 255)->nullable();
            $table->foreignId('cambiado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('cambiado_at')->useCurrent();
            $table->timestamps();
            $table->index(['pago_id', 'cambiado_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_estados_pago');
    }
};
