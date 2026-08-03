<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('numero_socio', 30)->unique();
            $table->foreignId('sexo_id')->nullable()->constrained('sexos')->restrictOnDelete();
            $table->foreignId('estado_cliente_id')->constrained('estados_cliente')->restrictOnDelete();
            $table->string('nombre', 100);
            $table->string('apellido', 100);
            $table->string('tipo_identificacion', 30)->nullable();
            $table->string('numero_identificacion', 60)->nullable();
            $table->string('telefono', 25)->nullable();
            $table->string('correo_electronico', 150)->nullable()->unique();
            $table->string('direccion', 200)->nullable();
            $table->string('ciudad', 100)->nullable();
            $table->string('pais', 2)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->date('fecha_registro')->default(DB::raw('(CURRENT_DATE)'));
            $table->string('motivo_estado', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['tipo_identificacion', 'numero_identificacion'], 'uq_cliente_identificacion');
            $table->index(['apellido', 'nombre'], 'idx_cliente_apellido_nombre');
            $table->index(['estado_cliente_id', 'deleted_at'], 'idx_cliente_estado');
        });
        DB::statement('ALTER TABLE clientes ADD CONSTRAINT chk_cliente_nacimiento CHECK (fecha_nacimiento IS NULL OR fecha_nacimiento <= fecha_registro)');
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
