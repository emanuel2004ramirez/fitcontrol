<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalles_evaluacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluacion_fisica_id')->constrained('evaluaciones_fisicas')->restrictOnDelete();
            $table->foreignId('tipo_medida_id')->constrained('tipos_medida')->restrictOnDelete();
            $table->decimal('valor', 12, 4);
            $table->string('unidad_snapshot', 20);
            $table->string('instrumento', 100)->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->unique(['evaluacion_fisica_id', 'tipo_medida_id'], 'uq_evaluacion_tipo_medida');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalles_evaluacion');
    }
};
