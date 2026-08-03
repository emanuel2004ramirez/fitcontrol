<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipos_membresia', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 40)->unique();
            $table->string('nombre', 100)->unique();
            $table->text('descripcion')->nullable();
            $table->unsignedSmallInteger('duracion_dias');
            $table->boolean('activo')->default(true)->index();
            $table->timestamps();
        });
        DB::statement('ALTER TABLE tipos_membresia ADD CONSTRAINT chk_tipo_membresia_duracion CHECK (duracion_dias > 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('tipos_membresia');
    }
};
