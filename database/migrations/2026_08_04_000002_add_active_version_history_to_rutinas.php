<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rutinas', function (Blueprint $t) {
            $t->foreignId('version_activa_id')->nullable()->after('estado_rutina_id')->constrained('versiones_rutina')->nullOnDelete();
        });
        Schema::create('historial_versiones_rutina', function (Blueprint $t) {
            $t->id();
            $t->foreignId('rutina_id')->constrained('rutinas')->restrictOnDelete();
            $t->foreignId('version_anterior_id')->nullable()->constrained('versiones_rutina')->nullOnDelete();
            $t->foreignId('version_nueva_id')->constrained('versiones_rutina')->restrictOnDelete();
            $t->string('motivo', 255);
            $t->foreignId('activada_por')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamp('activada_at')->useCurrent();
            $t->timestamps();
            $t->index(['rutina_id', 'activada_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_versiones_rutina');
        Schema::table('rutinas', function (Blueprint $t) {
            $t->dropConstrainedForeignId('version_activa_id');
        });
    }
};
