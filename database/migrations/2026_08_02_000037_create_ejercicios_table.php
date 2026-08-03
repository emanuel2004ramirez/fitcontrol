<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ejercicios', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 40)->unique();
            $table->foreignId('estado_ejercicio_id')->constrained('estados_ejercicio')->restrictOnDelete();
            $table->string('nombre', 120);
            $table->string('patron_movimiento', 80)->nullable();
            $table->string('equipamiento', 120)->nullable();
            $table->text('descripcion')->nullable();
            $table->text('instrucciones')->nullable();
            $table->string('video_url', 500)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['nombre', 'deleted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ejercicios');
    }
};
