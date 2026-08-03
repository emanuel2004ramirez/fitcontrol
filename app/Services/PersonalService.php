<?php

namespace App\Services;

class PersonalService extends StoredProcedureService
{
    public function listar(int $limite = 100, int $offset = 0): array
    {
        return $this->select('sp_personal_listar', [$limite, $offset]);
    }

    public function obtener(int $id): ?object
    {
        return $this->selectOne('sp_personal_obtener', [$id]);
    }

    public function buscar(?string $texto = null, ?int $estadoId = null): array
    {
        return $this->select('sp_personal_buscar', [$texto, $estadoId]);
    }

    public function crear(array $data): ?object
    {
        return $this->selectOne('sp_personal_crear', [$data['codigo_empleado'], $data['cargo_id'], $data['sexo_id'] ?? null, $data['estado_personal_id'], $data['nombre'], $data['apellido'], $data['tipo_identificacion'] ?? null, $data['numero_identificacion'] ?? null, $data['telefono'] ?? null, $data['correo_electronico'] ?? null, $data['fecha_contratacion']]);
    }

    public function actualizar(int $id, array $data): ?object
    {
        return $this->selectOne('sp_personal_actualizar', [$id, $data['cargo_id'], $data['sexo_id'] ?? null, $data['nombre'], $data['apellido'], $data['telefono'] ?? null, $data['correo_electronico'] ?? null]);
    }

    public function cambiarEstado(int $id, int $estadoId, ?string $motivo, ?int $usuarioId): bool
    {
        return $this->statement('sp_personal_cambiar_estado', [$id, $estadoId, $motivo, $usuarioId]);
    }

    public function eliminar(int $id, ?string $fechaTerminacion, string $motivo): bool
    {
        return $this->statement('sp_personal_eliminar', [$id, $fechaTerminacion, $motivo]);
    }

    public function guardarHorario(array $data): ?object
    {
        return $this->selectOne('sp_horarios_personal_guardar', [$data['id'] ?? null, $data['personal_id'], $data['dia_semana'], $data['hora_inicio'], $data['hora_fin'], $data['vigente_desde'], $data['vigente_hasta'] ?? null]);
    }

    public function eliminarHorario(int $id): bool
    {
        return $this->statement('sp_horarios_personal_eliminar', [$id]);
    }
}
