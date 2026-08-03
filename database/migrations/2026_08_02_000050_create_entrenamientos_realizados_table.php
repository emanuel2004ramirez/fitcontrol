<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entrenamientos_realizados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->restrictOnDelete();
            $table->foreignId('version_rutina_id')->nullable()->constrained('versiones_rutina')->restrictOnDelete();
            $table->foreignId('sesion_rutina_id')->nullable()->constrained('sesiones_rutina')->restrictOnDelete();
            $table->dateTime('iniciado_at');
            $table->dateTime('finalizado_at')->nullable();
            $table->unsignedTinyInteger('esfuerzo_percibido')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();
            $table->index(['cliente_id', 'iniciado_at']);
        });
        DB::statement('ALTER TABLE entrenamientos_realizados ADD CONSTRAINT chk_entrenamiento_tiempos CHECK (finalizado_at IS NULL OR finalizado_at > iniciado_at)');
        DB::statement('ALTER TABLE entrenamientos_realizados ADD CONSTRAINT chk_entrenamiento_esfuerzo CHECK (esfuerzo_percibido IS NULL OR esfuerzo_percibido BETWEEN 1 AND 10)');
    }

    public function down(): void
    {
        Schema::dropIfExists('entrenamientos_realizados');
    }
};
