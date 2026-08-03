<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personal_id')->nullable()->unique()->constrained('personal')->restrictOnDelete();
            $table->string('name');
            $table->string('username', 60)->unique();
            $table->string('email', 150)->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('activo')->default(true)->index();
            $table->boolean('debe_cambiar_password')->default(false);
            $table->unsignedSmallInteger('intentos_fallidos')->default(0);
            $table->timestamp('bloqueado_hasta')->nullable();
            $table->timestamp('password_changed_at')->nullable();
            $table->timestamp('ultimo_acceso_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
