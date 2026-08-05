<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pagos', fn (Blueprint $table) => $table->index('pagado_at', 'idx_pagos_dashboard_fecha'));
        Schema::table('asistencias', fn (Blueprint $table) => $table->index('entrada_at', 'idx_asistencias_dashboard_fecha'));
        Schema::table('membresias', fn (Blueprint $table) => $table->index(['bloqueo_activa', 'fecha_fin'], 'idx_membresias_dashboard_vigencia'));
    }

    public function down(): void
    {
        Schema::table('pagos', fn (Blueprint $table) => $table->dropIndex('idx_pagos_dashboard_fecha'));
        Schema::table('asistencias', fn (Blueprint $table) => $table->dropIndex('idx_asistencias_dashboard_fecha'));
        Schema::table('membresias', fn (Blueprint $table) => $table->dropIndex('idx_membresias_dashboard_vigencia'));
    }
};
