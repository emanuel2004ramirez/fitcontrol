<?php

namespace App\Services;

class EjercicioService extends StoredProcedureService
{
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
        return $this->selectOne('sp_ejercicios_crear', [$data['codigo'], $data['estado_ejercicio_id'], $data['nombre'], $data['patron_movimiento'] ?? null, $data['equipamiento'] ?? null, $data['descripcion'] ?? null, $data['instrucciones'] ?? null, $data['video_url'] ?? null]);
    }

    public function actualizar(int $id, array $data): bool
    {
        return $this->statement('sp_ejercicios_actualizar', [$id, $data['estado_ejercicio_id'], $data['nombre'], $data['patron_movimiento'] ?? null, $data['equipamiento'] ?? null, $data['descripcion'] ?? null, $data['instrucciones'] ?? null, $data['video_url'] ?? null]);
    }

    public function asignarGrupoMuscular(int $ejercicioId, int $grupoId, bool $principal = false): bool
    {
        return $this->statement('sp_ejercicios_asignar_grupo', [$ejercicioId, $grupoId, $principal]);
    }

    public function eliminar(int $id): bool
    {
        return $this->statement('sp_ejercicios_eliminar', [$id]);
    }
}
