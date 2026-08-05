<?php

namespace Database\Seeders;

use App\Services\CatalogoService;
use App\Services\UsuarioService;
use App\Support\Authorization\FitControlPermissions;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class AuthorizationSeeder extends Seeder
{
    public function run(): void
    {
        $catalogos = app(CatalogoService::class);
        $usuarios = app(UsuarioService::class);

        $permisosExistentes = collect($catalogos->listar('permisos', limite: 500))->keyBy('codigo');
        foreach (FitControlPermissions::all() as $permission) {
            $data = [
                'codigo' => $permission['codigo'],
                'nombre' => $permission['nombre'],
                'modulo' => $permission['modulo'],
                'descripcion' => $permission['descripcion'],
            ];
            $existente = $permisosExistentes->get($permission['codigo']);
            $existente === null
                ? $catalogos->crear('permisos', $data)
                : $catalogos->actualizar('permisos', (int) $existente->id, $data);
        }

        $roles = collect($catalogos->listar('roles'))->keyBy('codigo');
        $rol = $roles->get(FitControlPermissions::SUPER_ADMIN_ROLE);
        $rolData = ['codigo' => FitControlPermissions::SUPER_ADMIN_ROLE, 'nombre' => 'Super administrador', 'descripcion' => 'Acceso total al sistema.', 'activo' => true];
        $rol = $rol === null ? $catalogos->crear('roles', $rolData) : $catalogos->actualizar('roles', (int) $rol->id, $rolData);

        $permisos = $catalogos->listar('permisos', limite: 500);
        foreach ($permisos as $permiso) {
            $usuarios->asignarPermisoARol((int) $rol->id, (int) $permiso->id);
        }

        $admin = $usuarios->obtenerCredenciales('admin');
        if ($admin !== null) {
            $usuarios->asignarRol((int) $admin->id, (int) $rol->id);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
