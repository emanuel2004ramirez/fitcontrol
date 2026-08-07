<?php

namespace App\Console\Commands;

use App\Services\UsuarioService;
use App\Support\Authorization\FitControlPermissions;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class SetupAccessControl extends Command
{
    protected $signature = 'fitcontrol:setup-access {--password=FitControl2026!}';

    protected $description = 'Crea permisos, roles operativos y el usuario superadministrador';

    private const ROLES = [
        'super-admin' => ['Superadministrador', 'Acceso total al sistema.', ['*']],
        'gerencia' => ['Gerencia', 'Supervisión operativa y reportes.', ['clientes.*', 'personal.*', 'membresias.*', 'pagos.*', 'asistencias.*', 'ejercicios.*', 'rutinas.*', 'evaluaciones.*', 'entrenamientos.*', 'reportes.*', 'auditoria.viewAny', 'auditoria.view']],
        'recepcion' => ['Recepción', 'Clientes, membresías y control de accesos.', ['clientes.viewAny', 'clientes.view', 'clientes.create', 'clientes.update', 'clientes.delete', 'clientes.changeStatus', 'clientes.manage', 'membresias.*', 'asistencias.*', 'pagos.viewAny', 'pagos.view']],
        'caja' => ['Caja', 'Cobros, pagos y consultas comerciales.', ['pagos.*', 'clientes.viewAny', 'clientes.view', 'membresias.viewAny', 'membresias.view']],
        'entrenador' => ['Entrenador', 'Rutinas, ejercicios, evaluaciones y entrenamientos.', ['clientes.viewAny', 'clientes.view', 'clientes.medical', 'ejercicios.*', 'rutinas.*', 'evaluaciones.*', 'entrenamientos.*', 'asistencias.viewAny', 'asistencias.view', 'asistencias.update']],
    ];

    public function handle(UsuarioService $users): int
    {
        $permissions = [];
        foreach (FitControlPermissions::all() as $definition) {
            $permission = $users->guardarPermiso($definition);
            $permissions[$definition['codigo']] = (int) $permission->id;
        }

        $roles = [];
        foreach (self::ROLES as $code => [$name, $description, $rules]) {
            $role = $users->guardarRol($code, $name, $description);
            $roles[$code] = (int) $role->id;
            foreach ($permissions as $permission => $permissionId) {
                if ($this->matches($permission, $rules)) {
                    $users->asignarPermisoARol((int) $role->id, $permissionId);
                } else {
                    $users->retirarPermisoDeRol((int) $role->id, $permissionId);
                }
            }
        }

        $admin = $users->obtenerCredenciales('admin');
        if ($admin === null) {
            $admin = $users->crear([
                'personal_id' => null,
                'name' => 'Administrador FitControl',
                'username' => 'admin',
                'email' => 'admin@fitcontrol.local',
                'password_hash' => Hash::make((string) $this->option('password')),
                'debe_cambiar_password' => false,
            ]);
        } else {
            $users->cambiarPassword((int) $admin->id, Hash::make((string) $this->option('password')), false);
        }
        $users->asignarRol((int) $admin->id, $roles['super-admin']);

        $this->info('Acceso configurado. Usuario: admin');

        return self::SUCCESS;
    }

    private function matches(string $permission, array $rules): bool
    {
        foreach ($rules as $rule) {
            if ($rule === '*' || $rule === $permission || (str_ends_with($rule, '.*') && str_starts_with($permission, substr($rule, 0, -1)))) {
                return true;
            }
        }

        return false;
    }
}
