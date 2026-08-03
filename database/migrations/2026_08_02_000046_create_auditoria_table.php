<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auditoria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('evento', 40);
            $table->string('entidad', 120);
            $table->string('entidad_id', 64)->nullable();
            $table->json('valores_anteriores')->nullable();
            $table->json('valores_nuevos')->nullable();
            $table->string('motivo', 255)->nullable();
            $table->string('ip', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('ocurrido_at')->useCurrent();
            $table->index(['entidad', 'entidad_id', 'ocurrido_at'], 'idx_auditoria_entidad');
            $table->index(['user_id', 'ocurrido_at'], 'idx_auditoria_usuario');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auditoria');
    }
};
