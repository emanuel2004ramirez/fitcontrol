<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ejercicio_grupo_muscular', function (Blueprint $table) {
            $table->foreignId('ejercicio_id')->constrained('ejercicios')->cascadeOnDelete();
            $table->foreignId('grupo_muscular_id')->constrained('grupos_musculares')->restrictOnDelete();
            $table->boolean('es_principal')->default(false);
            $table->timestamps();
            $table->primary(['ejercicio_id', 'grupo_muscular_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ejercicio_grupo_muscular');
    }
};
