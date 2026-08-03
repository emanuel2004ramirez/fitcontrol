<?php

namespace App\Services;

class UsuarioService extends StoredProcedureService
{
    public function listar(int $limite = 100, int $offset = 0): array
    {
        return $this->select('sp_users_listar', [$limite, $offset]);
    }

    public function obtener(int $id): ?object
    {
        return $this->selectOne('sp_users_obtener', [$id]);
    }

    public function obtenerCredenciales(string $login): ?object
    {
        return $this->selectOne('sp_users_obtener_credenciales', [$login]);
    }

    public function crear(array $data): ?object
    {
        return $this->selectOne('sp_users_crear', [$data['personal_id'] ?? null, $data['name'], $data['username'], $data['email'] ?? null, $data['password_hash'], $data['debe_cambiar_password'] ?? false]);
    }

    public function actualizar(int $id, array $data): bool
    {
        return $this->statement('sp_users_actualizar', [$id, $data['name'], $data['username'], $data['email'] ?? null, $data['activo']]);
    }

    public function cambiarPassword(int $id, string $hash, bool $debeCambiar = false): bool
    {
        return $this->statement('sp_users_cambiar_password', [$id, $hash, $debeCambiar]);
    }

    public function asignarRol(int $usuarioId, int $rolId): bool
    {
        return $this->statement('sp_users_asignar_rol', [$usuarioId, $rolId]);
    }

    public function retirarRol(int $usuarioId, int $rolId): bool
    {
        return $this->statement('sp_users_retirar_rol', [$usuarioId, $rolId]);
    }

    public function asignarPermisoARol(int $rolId, int $permisoId): bool
    {
        return $this->statement('sp_roles_asignar_permiso', [$rolId, $permisoId]);
    }

    public function retirarPermisoDeRol(int $rolId, int $permisoId): bool
    {
        return $this->statement('sp_roles_retirar_permiso', [$rolId, $permisoId]);
    }

    public function eliminar(int $id): bool
    {
        return $this->statement('sp_users_eliminar', [$id]);
    }
}
