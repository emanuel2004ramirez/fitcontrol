<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membresias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->restrictOnDelete();
            $table->foreignId('tipo_membresia_id')->constrained('tipos_membresia')->restrictOnDelete();
            $table->foreignId('precio_membresia_id')->constrained('precios_membresia')->restrictOnDelete();
            $table->foreignId('estado_membresia_id')->constrained('estados_membresia')->restrictOnDelete();
            $table->foreignId('membresia_anterior_id')->nullable()->constrained('membresias')->nullOnDelete();
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->decimal('precio_contratado', 12, 2);
            $table->char('moneda', 3);
            $table->unsignedTinyInteger('bloqueo_activa')->nullable()->comment('Usar 1 solo para la membresía activa; NULL para historial');
            $table->string('origen', 30)->default('NUEVA');
            $table->timestamp('cancelada_at')->nullable();
            $table->string('motivo_cancelacion', 255)->nullable();
            $table->foreignId('creada_por')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('cancelada_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['cliente_id', 'bloqueo_activa'], 'uq_cliente_membresia_activa');
            $table->index(['cliente_id', 'estado_membresia_id', 'fecha_fin'], 'idx_membresia_cliente_estado_fin');
        });
        DB::statement('ALTER TABLE membresias ADD CONSTRAINT chk_membresia_fechas CHECK (fecha_fin >= fecha_inicio)');
        DB::statement('ALTER TABLE membresias ADD CONSTRAINT chk_membresia_precio CHECK (precio_contratado > 0)');
        DB::statement('ALTER TABLE membresias ADD CONSTRAINT chk_membresia_bloqueo CHECK (bloqueo_activa IS NULL OR bloqueo_activa = 1)');
    }

    public function down(): void
    {
        Schema::dropIfExists('membresias');
    }
};
