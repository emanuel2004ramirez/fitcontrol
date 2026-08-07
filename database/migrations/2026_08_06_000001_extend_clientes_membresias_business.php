<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('datos_medicos_cliente', function (Blueprint $table): void {
            $table->boolean('es_confidencial')->default(true)->after('contacto_medico');
        });

        Schema::create('contratos_membresia', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('membresia_id')->unique()->constrained('membresias')->restrictOnDelete();
            $table->string('numero_contrato', 40)->unique();
            $table->string('version_documento', 30);
            $table->string('cliente_nombre', 220);
            $table->string('cliente_identificacion', 100)->nullable();
            $table->string('plan_nombre', 120);
            $table->decimal('precio', 12, 2);
            $table->char('moneda', 3);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->longText('condiciones');
            $table->longText('politica_congelacion');
            $table->longText('politica_cancelacion');
            $table->string('firma_nombre', 220);
            $table->boolean('aceptado')->default(true);
            $table->timestamp('aceptado_at');
            $table->string('ip_aceptacion', 45)->nullable();
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('categorias_cancelacion_membresia', function (Blueprint $table): void {
            $table->id();
            $table->string('codigo', 40)->unique();
            $table->string('nombre', 100);
            $table->boolean('activo')->default(true);
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();
        });

        Schema::create('cancelaciones_membresia', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('membresia_id')->unique()->constrained('membresias')->restrictOnDelete();
            $table->foreignId('categoria_id')->constrained('categorias_cancelacion_membresia')->restrictOnDelete();
            $table->date('fecha_efectiva');
            $table->string('motivo', 255);
            $table->text('observaciones')->nullable();
            $table->foreignId('cancelada_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('cancelada_at');
            $table->timestamps();
        });

        Schema::create('membresias_familiares', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('membresia_id')->unique()->constrained('membresias')->restrictOnDelete();
            $table->foreignId('titular_cliente_id')->constrained('clientes')->restrictOnDelete();
            $table->foreignId('responsable_pago_cliente_id')->constrained('clientes')->restrictOnDelete();
            $table->unsignedSmallInteger('limite_beneficiarios')->default(3);
            $table->timestamps();
        });

        Schema::create('beneficiarios_membresia', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('membresia_familiar_id')->constrained('membresias_familiares')->restrictOnDelete();
            $table->foreignId('cliente_id')->constrained('clientes')->restrictOnDelete();
            $table->string('parentesco', 60);
            $table->string('estado', 20)->default('ACTIVO');
            $table->date('fecha_incorporacion');
            $table->date('fecha_retiro')->nullable();
            $table->string('motivo_retiro', 255)->nullable();
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['membresia_familiar_id', 'cliente_id'], 'uq_familia_beneficiario');
        });

        Schema::create('notificaciones_membresia', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('membresia_id')->constrained('membresias')->restrictOnDelete();
            $table->string('tipo', 40);
            $table->string('destinatario', 150);
            $table->string('estado', 20)->default('PENDIENTE');
            $table->timestamp('enviado_at')->nullable();
            $table->text('error')->nullable();
            $table->unsignedSmallInteger('intentos')->default(0);
            $table->timestamps();
            $table->unique(['membresia_id', 'tipo'], 'uq_notificacion_membresia_tipo');
        });

        Schema::create('eventos_cliente', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->restrictOnDelete();
            $table->string('tipo', 60);
            $table->string('titulo', 150);
            $table->text('descripcion')->nullable();
            $table->string('entidad', 80)->nullable();
            $table->unsignedBigInteger('entidad_id')->nullable();
            $table->json('metadatos')->nullable();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('ocurrido_at');
            $table->timestamps();
            $table->index(['cliente_id', 'ocurrido_at'], 'idx_eventos_cliente_fecha');
        });

        DB::table('categorias_cancelacion_membresia')->insert([
            ['codigo' => 'ECONOMICA', 'nombre' => 'Razones económicas', 'orden' => 10, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'TIEMPO', 'nombre' => 'Falta de tiempo', 'orden' => 20, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'SALUD', 'nombre' => 'Salud', 'orden' => 30, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'MUDANZA', 'nombre' => 'Mudanza', 'orden' => 40, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'INSATISFACCION', 'nombre' => 'Insatisfacción', 'orden' => 50, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'OTRO_GIMNASIO', 'nombre' => 'Cambio de gimnasio', 'orden' => 60, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'INCUMPLIMIENTO', 'nombre' => 'Incumplimiento', 'orden' => 70, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'ADMINISTRATIVA', 'nombre' => 'Decisión administrativa', 'orden' => 80, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'OTRO', 'nombre' => 'Otro', 'orden' => 90, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('eventos_cliente');
        Schema::dropIfExists('notificaciones_membresia');
        Schema::dropIfExists('beneficiarios_membresia');
        Schema::dropIfExists('membresias_familiares');
        Schema::dropIfExists('cancelaciones_membresia');
        Schema::dropIfExists('categorias_cancelacion_membresia');
        Schema::dropIfExists('contratos_membresia');
        Schema::table('datos_medicos_cliente', function (Blueprint $table): void {
            $table->dropColumn('es_confidencial');
        });
    }
};
