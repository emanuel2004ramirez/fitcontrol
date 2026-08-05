<?php

namespace Database\Seeders;

use App\Services\CatalogoService;
use App\Services\PersonalService;
use App\Services\UsuarioService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class DemoUserSeeder extends Seeder
{
    private const USERS = [
        ['name' => 'Administrador FitControl', 'username' => 'admin', 'email' => 'admin@fitcontrol.local', 'role' => 'super-admin', 'employee' => null],
        ['name' => 'Maria Fernanda Lopez', 'username' => 'maria', 'email' => 'maria.lopez@fitcontrol.local', 'role' => 'gerencia', 'employee' => 'EMP-001'],
        ['name' => 'Andrea Martinez', 'username' => 'andrea', 'email' => 'andrea.martinez@fitcontrol.local', 'role' => 'recepcion', 'employee' => 'EMP-002'],
        ['name' => 'Sofia Rivera', 'username' => 'sofia', 'email' => 'sofia.rivera@fitcontrol.local', 'role' => 'caja', 'employee' => 'EMP-004'],
        ['name' => 'Jose Castillo', 'username' => 'jose', 'email' => 'jose.castillo@fitcontrol.local', 'role' => 'entrenador', 'employee' => 'EMP-005'],
    ];

    public function run(): void
    {
        $users = app(UsuarioService::class);
        $roles = collect(app(CatalogoService::class)->listar('roles', limite: 100))->keyBy('codigo');
        $employees = collect(app(PersonalService::class)->buscar())->keyBy('codigo_empleado');
        $password = Hash::make('123456');

        foreach (self::USERS as $definition) {
            $role = $roles->get($definition['role']);
            if ($role === null) {
                throw new RuntimeException("El rol {$definition['role']} no esta configurado.");
            }

            $employee = $definition['employee'] === null ? null : $employees->get($definition['employee']);
            if ($definition['employee'] !== null && $employee === null) {
                throw new RuntimeException("El empleado {$definition['employee']} no esta configurado.");
            }

            $user = $users->obtenerCredenciales($definition['email']) ?? $users->obtenerCredenciales($definition['username']);
            if ($user === null) {
                $user = $users->crear([
                    'personal_id' => $employee?->id,
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

            $users->vincularPersonal((int) $user->id, $employee?->id);
            $users->asignarRol((int) $user->id, (int) $role->id);
        }
    }
}
