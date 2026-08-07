<?php

namespace App\Services;

use Illuminate\Pagination\LengthAwarePaginator;

class EjercicioService extends StoredProcedureService
{
    public function paginar(array $f, int $per, int $page): LengthAwarePaginator
    {
        $p = [$f['texto'] ?? null, $f['estado_id'] ?? null, $f['grupo_id'] ?? null, $f['equipamiento_id'] ?? null];

        return $this->paginateProcedures('sp_ejercicios_contar', 'sp_ejercicios_filtrar', $p, $per, $page, $f);
    }

    public function grupos(int $id): array
    {
        return $this->select('sp_ejercicios_grupos', [$id]);
    }

    public function retirarGrupoMuscular(int $ejercicio, int $grupo): bool
    {
        return $this->statement('sp_ejercicios_retirar_grupo', [$ejercicio, $grupo]);
    }

    public function cambiarEstado(int $id, int $estado): bool
    {
        return $this->statement('sp_ejercicios_cambiar_estado', [$id, $estado]);
    }

    public function listar(int $limite = 100, int $offset = 0): array
    {
        return $this->select('sp_ejercicios_listar', [$limite, $offset]);
    }

    public function obtener(int $id): ?object
    {
        return $this->selectOne('sp_ejercicios_obtener', [$id]);
    }

    public function crear(array $data): ?object
    {
        return $this->selectOne('sp_ejercicios_crear', [null, $data['nombre'], $data['grupo_muscular_id'], $data['descripcion'] ?? null, $data['instrucciones'] ?? null, $data['video_url'] ?? null]);
    }

    public function actualizar(int $id, array $data): bool
    {
        return $this->statement('sp_ejercicios_actualizar', [$id, $data['estado_ejercicio_id'], $data['nombre'], $data['grupo_muscular_id'], $data['descripcion'] ?? null, $data['instrucciones'] ?? null, $data['video_url'] ?? null]);
    }

    public function equipamientosActivos(): array
    {
        return $this->select('sp_ejercicios_equipamientos_activos');
    }

    public function sincronizarEquipamientos(int $ejercicioId, array $ids): void
    {
        $this->statement('sp_ejercicios_sincronizar_equipamientos', [$ejercicioId, json_encode(array_values(array_unique(array_map('intval', $ids))), JSON_THROW_ON_ERROR)]);
    }

    public function asignarGrupoMuscular(int $ejercicioId, int $grupoId): bool
    {
        return $this->statement('sp_ejercicios_asignar_grupo', [$ejercicioId, $grupoId]);
    }

    public function eliminar(int $id): bool
    {
        return $this->statement('sp_ejercicios_eliminar', [$id]);
    }
}
