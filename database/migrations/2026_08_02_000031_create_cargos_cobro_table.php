<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cargos_cobro', function (Blueprint $table) {
            $table->id();
            $table->string('numero_cargo', 40)->unique();
            $table->foreignId('cliente_id')->constrained('clientes')->restrictOnDelete();
            $table->foreignId('membresia_id')->nullable()->constrained('membresias')->restrictOnDelete();
            $table->foreignId('estado_cargo_cobro_id')->constrained('estados_cargo_cobro')->restrictOnDelete();
            $table->string('concepto', 150);
            $table->text('descripcion')->nullable();
            $table->decimal('subtotal', 12, 2);
            $table->decimal('descuento', 12, 2)->default(0);
            $table->decimal('impuesto', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->char('moneda', 3);
            $table->date('fecha_emision')->default(DB::raw('(CURRENT_DATE)'));
            $table->date('fecha_vencimiento')->nullable();
            $table->foreignId('creado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['cliente_id', 'estado_cargo_cobro_id', 'fecha_vencimiento'], 'idx_cargo_cliente_estado');
        });
        DB::statement('ALTER TABLE cargos_cobro ADD CONSTRAINT chk_cargo_montos CHECK (subtotal >= 0 AND descuento >= 0 AND impuesto >= 0 AND total >= 0)');
        DB::statement('ALTER TABLE cargos_cobro ADD CONSTRAINT chk_cargo_total CHECK (total = subtotal - descuento + impuesto)');
        DB::statement('ALTER TABLE cargos_cobro ADD CONSTRAINT chk_cargo_fechas CHECK (fecha_vencimiento IS NULL OR fecha_vencimiento >= fecha_emision)');
    }

    public function down(): void
    {
        Schema::dropIfExists('cargos_cobro');
    }
};
