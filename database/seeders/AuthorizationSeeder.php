<?php

namespace Database\Seeders;

use App\Models\Permiso;
use App\Models\Rol;
use App\Models\User;
use App\Support\Authorization\FitControlPermissions;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class AuthorizationSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (FitControlPermissions::all() as $permission) {
            Permiso::query()->updateOrCreate(
                ['name' => $permission['codigo'], 'guard_name' => 'web'],
                [
                    'codigo' => $permission['codigo'],
                    'nombre' => $permission['nombre'],
                    'modulo' => $permission['modulo'],
                    'descripcion' => $permission['descripcion'],
                ]
            );
        }

        $superAdmin = Rol::query()->updateOrCreate(
            ['name' => FitControlPermissions::SUPER_ADMIN_ROLE, 'guard_name' => 'web'],
            [
                'codigo' => FitControlPermissions::SUPER_ADMIN_ROLE,
                'nombre' => 'Super administrador',
                'descripcion' => 'Acceso total al sistema.',
                'activo' => true,
            ]
        );

        $superAdmin->syncPermissions(Permiso::query()->pluck('name')->all());

        User::query()
            ->where('username', 'admin')
            ->orWhere('email', 'test@example.com')
            ->get()
            ->each(fn (User $user) => $user->assignRole($superAdmin));

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
