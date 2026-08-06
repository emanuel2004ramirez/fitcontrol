<?php

namespace App\Services;

class UsuarioService extends StoredProcedureService
{
    public function listar(int $limite = 100, int $offset = 0): array
    {
        return $this->select('sp_users_admin_listar');
    }

    public function personalDisponible(?int $usuarioId = null): array
    {
        return $this->select('sp_users_personal_disponible', [$usuarioId]);
    }

    public function obtener(int $id): ?object
    {
        return $this->selectOne('sp_users_obtener', [$id]);
    }

    public function obtenerCredenciales(string $login): ?object
    {
        return $this->selectOne('sp_users_obtener_credenciales', [$login]);
    }

    public function obtenerParaAutenticacion(int $id): ?object
    {
        return $this->selectOne('sp_auth_user_by_id', [$id]);
    }

    public function registrarLoginExitoso(int $id): bool
    {
        return $this->statement('sp_auth_login_exitoso', [$id]);
    }

    public function registrarLoginFallido(string $login): bool
    {
        return $this->statement('sp_auth_login_fallido', [$login]);
    }

    public function roles(int $id): array
    {
        return $this->select('sp_auth_user_roles', [$id]);
    }

    public function permisos(int $id): array
    {
        return $this->select('sp_auth_user_permissions', [$id]);
    }

    public function perfil(int $id): ?object
    {
        return $this->selectOne('sp_auth_user_profile', [$id]);
    }

    public function guardarRol(string $codigo, string $nombre, string $descripcion): ?object
    {
        return $this->selectOne('sp_auth_upsert_role', [$codigo, $nombre, $descripcion]);
    }

    public function guardarPermiso(array $permiso): ?object
    {
        return $this->selectOne('sp_auth_upsert_permission', [$permiso['codigo'], $permiso['nombre'], $permiso['modulo'], $permiso['descripcion']]);
    }

    public function crear(array $data): ?object
    {
        return $this->selectOne('sp_users_crear_con_rol', [$data['personal_id'] ?? null, $data['name'], $data['username'], $data['email'] ?? null, $data['password_hash'], $data['debe_cambiar_password'] ?? true, $data['rol_id']]);
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

    public function vincularPersonal(int $usuarioId, ?int $personalId): bool
    {
        return $this->statement('sp_users_vincular_personal', [$usuarioId, $personalId]);
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
