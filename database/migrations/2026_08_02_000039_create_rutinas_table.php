<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rutinas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->restrictOnDelete();
            $table->foreignId('entrenador_id')->constrained('personal')->restrictOnDelete();
            $table->foreignId('estado_rutina_id')->constrained('estados_rutina')->restrictOnDelete();
            $table->string('nombre', 120);
            $table->text('descripcion')->nullable();
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->foreignId('creada_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['cliente_id', 'estado_rutina_id', 'fecha_inicio'], 'idx_rutina_cliente_estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rutinas');
    }
};
