<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ejercicio_grupo_muscular', function (Blueprint $table) {
            $table->dropColumn('es_principal');
        });
    }

    public function down(): void
    {
        Schema::table('ejercicio_grupo_muscular', function (Blueprint $table) {
            $table->boolean('es_principal')->default(false);
        });
    }
};
