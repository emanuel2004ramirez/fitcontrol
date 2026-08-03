<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('versiones_rutina', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rutina_id')->constrained('rutinas')->restrictOnDelete();
            $table->unsignedInteger('numero_version');
            $table->text('notas_cambio')->nullable();
            $table->timestamp('publicada_at')->nullable();
            $table->foreignId('creada_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['rutina_id', 'numero_version']);
            $table->index(['rutina_id', 'publicada_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('versiones_rutina');
    }
};
