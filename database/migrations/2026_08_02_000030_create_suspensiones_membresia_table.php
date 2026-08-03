<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suspensiones_membresia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('membresia_id')->constrained('membresias')->restrictOnDelete();
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->unsignedSmallInteger('dias_extension')->default(0);
            $table->string('motivo', 255);
            $table->foreignId('autorizada_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['membresia_id', 'fecha_inicio', 'fecha_fin'], 'idx_suspension_periodo');
        });
        DB::statement('ALTER TABLE suspensiones_membresia ADD CONSTRAINT chk_suspension_fechas CHECK (fecha_fin IS NULL OR fecha_fin >= fecha_inicio)');
    }

    public function down(): void
    {
        Schema::dropIfExists('suspensiones_membresia');
    }
};
