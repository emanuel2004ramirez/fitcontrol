<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asistencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->restrictOnDelete();
            $table->foreignId('membresia_id')->constrained('membresias')->restrictOnDelete();
            $table->dateTime('entrada_at');
            $table->dateTime('salida_at')->nullable();
            $table->unsignedTinyInteger('bloqueo_abierta')->nullable()->comment('Usar 1 mientras la visita esté abierta; NULL al cerrarla');
            $table->string('metodo_registro', 30)->default('MANUAL');
            $table->foreignId('entrada_registrada_por')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('salida_registrada_por')->nullable()->constrained('users')->nullOnDelete();
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->unique(['cliente_id', 'bloqueo_abierta'], 'uq_cliente_asistencia_abierta');
            $table->index(['cliente_id', 'entrada_at'], 'idx_asistencia_cliente_entrada');
        });
        DB::statement('ALTER TABLE asistencias ADD CONSTRAINT chk_asistencia_tiempos CHECK (salida_at IS NULL OR salida_at > entrada_at)');
        DB::statement('ALTER TABLE asistencias ADD CONSTRAINT chk_asistencia_bloqueo CHECK (bloqueo_abierta IS NULL OR bloqueo_abierta = 1)');
    }

    public function down(): void
    {
        Schema::dropIfExists('asistencias');
    }
};
