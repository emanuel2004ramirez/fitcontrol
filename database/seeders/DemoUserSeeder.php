<?php

namespace Database\Seeders;

use App\Services\CatalogoService;
use App\Services\UsuarioService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class DemoUserSeeder extends Seeder
{
    private const USERS = [
        ['name' => 'Administrador FitControl', 'username' => 'admin', 'email' => 'admin@fitcontrol.local', 'role' => 'super-admin'],
        ['name' => 'Gerente General', 'username' => 'gerencia', 'email' => 'gerencia@fitcontrol.local', 'role' => 'gerencia'],
        ['name' => 'Recepción FitControl', 'username' => 'recepcion', 'email' => 'recepcion@fitcontrol.local', 'role' => 'recepcion'],
        ['name' => 'Caja FitControl', 'username' => 'caja', 'email' => 'caja@fitcontrol.local', 'role' => 'caja'],
        ['name' => 'Entrenador FitControl', 'username' => 'entrenador', 'email' => 'entrenador@fitcontrol.local', 'role' => 'entrenador'],
    ];

    public function run(): void
    {
        $users = app(UsuarioService::class);
        $roles = collect(app(CatalogoService::class)->listar('roles', limite: 100))->keyBy('codigo');
        $password = Hash::make('123456');

        foreach (self::USERS as $definition) {
            $role = $roles->get($definition['role']);
            if ($role === null) {
                throw new RuntimeException("El rol {$definition['role']} no está configurado.");
            }

            $user = $users->obtenerCredenciales($definition['username']);
            if ($user === null) {
                $user = $users->crear([
                    'personal_id' => null,
                    'name' => $definition['name'],
                    'username' => $definition['username'],
                    'email' => $definition['email'],
                    'password_hash' => $password,
                    'debe_cambiar_password' => false,
                ]);
            } else {
                $users->actualizar((int) $user->id, [
                    'name' => $definition['name'],
                    'username' => $definition['username'],
                    'email' => $definition['email'],
                    'activo' => true,
                ]);
                $users->cambiarPassword((int) $user->id, $password, false);
            }

            $users->asignarRol((int) $user->id, (int) $role->id);
        }
    }
}
