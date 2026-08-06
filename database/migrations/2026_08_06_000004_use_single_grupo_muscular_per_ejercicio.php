<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ejercicios', function (Blueprint $table) {
            $table->foreignId('grupo_muscular_id')->nullable()->after('equipamiento_id')->constrained('grupos_musculares')->nullOnDelete();
        });
        DB::statement('UPDATE ejercicios e JOIN (SELECT ejercicio_id, MIN(grupo_muscular_id) grupo_muscular_id FROM ejercicio_grupo_muscular GROUP BY ejercicio_id) x ON x.ejercicio_id = e.id SET e.grupo_muscular_id = x.grupo_muscular_id');
        Schema::dropIfExists('ejercicio_grupo_muscular');
    }

    public function down(): void
    {
        Schema::create('ejercicio_grupo_muscular', function (Blueprint $table) {
            $table->foreignId('ejercicio_id')->constrained('ejercicios')->cascadeOnDelete();
            $table->foreignId('grupo_muscular_id')->constrained('grupos_musculares')->restrictOnDelete();
            $table->timestamps();
            $table->primary(['ejercicio_id', 'grupo_muscular_id']);
        });
        Schema::table('ejercicios', fn (Blueprint $table) => $table->dropConstrainedForeignId('grupo_muscular_id'));
    }
};
