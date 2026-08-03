<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluaciones_fisicas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->restrictOnDelete();
            $table->foreignId('evaluador_id')->constrained('personal')->restrictOnDelete();
            $table->dateTime('evaluada_at');
            $table->string('metodo', 100)->nullable();
            $table->text('observaciones')->nullable();
            $table->foreignId('creada_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['cliente_id', 'evaluada_at'], 'idx_evaluacion_cliente_fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluaciones_fisicas');
    }
};
