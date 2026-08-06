<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void { Schema::create('equipamientos', function (Blueprint $t) { $t->id(); $t->string('codigo', 50)->unique(); $t->string('nombre', 120)->unique(); $t->boolean('activo')->default(true); $t->timestamps(); }); Schema::table('ejercicios', function (Blueprint $t) { $t->foreignId('equipamiento_id')->nullable()->after('estado_ejercicio_id')->constrained('equipamientos')->nullOnDelete(); $t->dropColumn(['patron_movimiento', 'equipamiento']); }); }
    public function down(): void { Schema::table('ejercicios', function (Blueprint $t) { $t->string('patron_movimiento',80)->nullable(); $t->string('equipamiento',120)->nullable(); $t->dropConstrainedForeignId('equipamiento_id'); }); Schema::dropIfExists('equipamientos'); }
};
