<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ejercicio_equipamiento', function (Blueprint $table) {
            $table->foreignId('ejercicio_id')->constrained('ejercicios')->cascadeOnDelete();
            $table->foreignId('equipamiento_id')->constrained('equipamientos')->restrictOnDelete();
            $table->timestamps();
            $table->primary(['ejercicio_id', 'equipamiento_id']);
        });
        DB::statement('INSERT INTO ejercicio_equipamiento (ejercicio_id, equipamiento_id, created_at, updated_at) SELECT id, equipamiento_id, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP FROM ejercicios WHERE equipamiento_id IS NOT NULL');
        Schema::table('ejercicios', fn (Blueprint $table) => $table->dropConstrainedForeignId('equipamiento_id'));
    }

    public function down(): void
    {
        Schema::table('ejercicios', fn (Blueprint $table) => $table->foreignId('equipamiento_id')->nullable()->constrained('equipamientos')->nullOnDelete());
        DB::statement('UPDATE ejercicios e JOIN (SELECT ejercicio_id, MIN(equipamiento_id) equipamiento_id FROM ejercicio_equipamiento GROUP BY ejercicio_id) x ON x.ejercicio_id=e.id SET e.equipamiento_id=x.equipamiento_id');
        Schema::dropIfExists('ejercicio_equipamiento');
    }
};
