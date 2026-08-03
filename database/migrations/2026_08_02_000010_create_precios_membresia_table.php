<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('precios_membresia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_membresia_id')->constrained('tipos_membresia')->restrictOnDelete();
            $table->decimal('precio', 12, 2);
            $table->char('moneda', 3)->default('HNL');
            $table->date('vigente_desde');
            $table->date('vigente_hasta')->nullable();
            $table->string('motivo_cambio', 255)->nullable();
            $table->timestamps();
            $table->unique(['tipo_membresia_id', 'moneda', 'vigente_desde'], 'uq_precio_inicio');
            $table->index(['tipo_membresia_id', 'moneda', 'vigente_hasta'], 'idx_precio_vigencia');
        });
        DB::statement('ALTER TABLE precios_membresia ADD CONSTRAINT chk_precio_positivo CHECK (precio > 0)');
        DB::statement('ALTER TABLE precios_membresia ADD CONSTRAINT chk_precio_periodo CHECK (vigente_hasta IS NULL OR vigente_hasta >= vigente_desde)');
    }

    public function down(): void
    {
        Schema::dropIfExists('precios_membresia');
    }
};
