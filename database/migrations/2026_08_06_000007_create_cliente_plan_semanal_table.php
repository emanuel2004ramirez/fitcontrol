<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { Schema::create('cliente_plan_semanal', function (Blueprint $t) { $t->id(); $t->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete(); $t->unsignedTinyInteger('dia_semana'); $t->foreignId('rutina_id')->nullable()->constrained('rutinas')->nullOnDelete(); $t->boolean('es_descanso')->default(false); $t->timestamps(); $t->unique(['cliente_id','dia_semana']); }); } public function down(): void { Schema::dropIfExists('cliente_plan_semanal'); } };
