<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contactos_emergencia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->string('nombre_completo', 150);
            $table->string('parentesco', 60);
            $table->string('telefono', 25);
            $table->boolean('es_principal')->default(false);
            $table->timestamps();
            $table->index(['cliente_id', 'es_principal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contactos_emergencia');
    }
};
