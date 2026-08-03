<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('objetivo_rutina', function (Blueprint $table) {
            $table->foreignId('rutina_id')->constrained('rutinas')->cascadeOnDelete();
            $table->foreignId('objetivo_id')->constrained('objetivos')->restrictOnDelete();
            $table->timestamps();
            $table->primary(['rutina_id', 'objetivo_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('objetivo_rutina');
    }
};
