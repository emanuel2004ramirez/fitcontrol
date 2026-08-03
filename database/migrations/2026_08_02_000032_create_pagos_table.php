<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->uuid('idempotency_key')->unique();
            $table->string('numero_recibo', 40)->unique();
            $table->foreignId('cliente_id')->constrained('clientes')->restrictOnDelete();
            $table->foreignId('metodo_pago_id')->constrained('metodos_pago')->restrictOnDelete();
            $table->foreignId('estado_pago_id')->constrained('estados_pago')->restrictOnDelete();
            $table->decimal('monto', 12, 2);
            $table->char('moneda', 3);
            $table->string('referencia', 120)->nullable();
            $table->string('referencia_externa', 150)->nullable()->unique();
            $table->timestamp('pagado_at')->nullable();
            $table->foreignId('procesado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->index(['cliente_id', 'estado_pago_id', 'created_at'], 'idx_pago_cliente_estado_fecha');
            $table->index(['metodo_pago_id', 'pagado_at'], 'idx_pago_metodo_fecha');
        });
        DB::statement('ALTER TABLE pagos ADD CONSTRAINT chk_pago_monto CHECK (monto > 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
