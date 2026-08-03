<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('datos_medicos_cliente', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->unique()->constrained('clientes')->restrictOnDelete();
            $table->text('condiciones_medicas')->nullable();
            $table->text('alergias')->nullable();
            $table->text('medicamentos')->nullable();
            $table->text('restricciones_ejercicio')->nullable();
            $table->string('contacto_medico', 150)->nullable();
            $table->timestamp('actualizado_at')->nullable();
            $table->foreignId('actualizado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('datos_medicos_cliente');
    }
};
