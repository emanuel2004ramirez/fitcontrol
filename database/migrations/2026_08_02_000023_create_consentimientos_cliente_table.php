<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consentimientos_cliente', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->restrictOnDelete();
            $table->string('tipo', 60);
            $table->string('version_documento', 30);
            $table->boolean('aceptado');
            $table->timestamp('registrado_at');
            $table->string('ip', 45)->nullable();
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['cliente_id', 'tipo', 'version_documento'], 'uq_consentimiento_version');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consentimientos_cliente');
    }
};
