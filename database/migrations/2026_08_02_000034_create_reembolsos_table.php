<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reembolsos', function (Blueprint $table) {
            $table->id();
            $table->string('numero_reembolso', 40)->unique();
            $table->foreignId('pago_id')->constrained('pagos')->restrictOnDelete();
            $table->decimal('monto', 12, 2);
            $table->char('moneda', 3);
            $table->string('motivo', 255);
            $table->string('referencia_externa', 150)->nullable()->unique();
            $table->timestamp('procesado_at');
            $table->foreignId('procesado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['pago_id', 'procesado_at']);
        });
        DB::statement('ALTER TABLE reembolsos ADD CONSTRAINT chk_reembolso_monto CHECK (monto > 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('reembolsos');
    }
};
