<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('equipamientos')) {
            Schema::create('equipamientos', function (Blueprint $table) {
                $table->id();
                $table->string('codigo', 50)->unique();
                $table->string('nombre', 120)->unique();
                $table->boolean('activo')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasColumn('ejercicios', 'equipamiento_id')) {
            Schema::table('ejercicios', function (Blueprint $table) {
                $table->foreignId('equipamiento_id')->nullable()->after('estado_ejercicio_id')->constrained('equipamientos')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        // Migración de reconciliación: no elimina datos o estructuras existentes.
    }
};
