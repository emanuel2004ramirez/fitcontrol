<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            if (! Schema::hasColumn('roles', 'name')) {
                $table->string('name')->nullable()->after('id');
            }

            if (! Schema::hasColumn('roles', 'guard_name')) {
                $table->string('guard_name', 25)->default('web')->after('name');
            }
        });

        DB::table('roles')->whereNull('name')->update([
            'name' => DB::raw('codigo'),
            'guard_name' => 'web',
        ]);

        Schema::table('roles', function (Blueprint $table) {
            $table->unique(['name', 'guard_name'], 'roles_name_guard_name_unique');
        });

        Schema::table('permisos', function (Blueprint $table) {
            if (! Schema::hasColumn('permisos', 'name')) {
                $table->string('name')->nullable()->after('id');
            }

            if (! Schema::hasColumn('permisos', 'guard_name')) {
                $table->string('guard_name', 25)->default('web')->after('name');
            }
        });

        DB::table('permisos')->whereNull('name')->update([
            'name' => DB::raw('codigo'),
            'guard_name' => 'web',
        ]);

        Schema::table('permisos', function (Blueprint $table) {
            $table->unique(['name', 'guard_name'], 'permisos_name_guard_name_unique');
        });

        Schema::table('permiso_rol', function (Blueprint $table) {
            if (Schema::hasColumn('permiso_rol', 'rol_id') && ! Schema::hasColumn('permiso_rol', 'role_id')) {
                $table->renameColumn('rol_id', 'role_id');
            }
        });

        Schema::table('role_user', function (Blueprint $table) {
            if (! Schema::hasColumn('role_user', 'model_type')) {
                $table->string('model_type')->default(App\Models\User::class)->after('user_id');
            }

            $table->index(['user_id', 'model_type'], 'role_user_user_id_model_type_index');
        });

        if (! Schema::hasTable('model_has_permissions')) {
            Schema::create('model_has_permissions', function (Blueprint $table) {
                $table->foreignId('permiso_id')->constrained('permisos')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('model_type')->default(App\Models\User::class);
                $table->index(['user_id', 'model_type'], 'model_has_permissions_user_id_model_type_index');
                $table->primary(['permiso_id', 'user_id', 'model_type'], 'model_has_permissions_primary');
            });
        }

        app('cache')->store(config('permission.cache.store') !== 'default' ? config('permission.cache.store') : null)
            ->forget(config('permission.cache.key'));
    }

    public function down(): void
    {
        Schema::dropIfExists('model_has_permissions');

        Schema::table('role_user', function (Blueprint $table) {
            if (Schema::hasColumn('role_user', 'model_type')) {
                $table->dropIndex('role_user_user_id_model_type_index');
                $table->dropColumn('model_type');
            }
        });

        Schema::table('permisos', function (Blueprint $table) {
            $table->dropUnique('permisos_name_guard_name_unique');
            $table->dropColumn(['name', 'guard_name']);
        });

        Schema::table('permiso_rol', function (Blueprint $table) {
            if (Schema::hasColumn('permiso_rol', 'role_id') && ! Schema::hasColumn('permiso_rol', 'rol_id')) {
                $table->renameColumn('role_id', 'rol_id');
            }
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->dropUnique('roles_name_guard_name_unique');
            $table->dropColumn(['name', 'guard_name']);
        });
    }
};
