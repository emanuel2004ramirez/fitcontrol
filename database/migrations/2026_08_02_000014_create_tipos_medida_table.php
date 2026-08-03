<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipos_medida', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 40)->unique();
            $table->string('nombre', 100)->unique();
            $table->string('unidad', 20);
            $table->decimal('valor_minimo', 12, 4)->nullable();
            $table->decimal('valor_maximo', 12, 4)->nullable();
            $table->unsignedTinyInteger('decimales')->default(2);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
        DB::statement('ALTER TABLE tipos_medida ADD CONSTRAINT chk_tipo_medida_rango CHECK (valor_maximo IS NULL OR valor_minimo IS NULL OR valor_maximo >= valor_minimo)');
    }

    public function down(): void
    {
        Schema::dropIfExists('tipos_medida');
    }
};
