<?php

namespace App\Services\Catalogos;

use App\Services\StoredProcedureService;

class GrupoMuscularService extends StoredProcedureService
{
    public function obtenerTodos(string $buscar = '', int $limite = 100, int $offset = 0): array
    {
        return $this->select('sp_grupos_musculares_listar', [
            $buscar,
            $limite,
            $offset
        ]);
    }

    public function crear(array $datos): void
    {
        $this->statement('sp_grupos_musculares_crear', [
            $datos['codigo'],
            $datos['nombre'],
            $datos['activo'] ?? 1,
        ]);
    }

    public function actualizar(int $id, array $datos): void
    {
        $this->statement('sp_grupos_musculares_actualizar', [
            $id,
            $datos['codigo'],
            $datos['nombre'],
            $datos['activo'] ?? 0,
        ]);
    }

    public function eliminar(int $id): void
    {
        $this->statement('sp_grupos_musculares_eliminar', [$id]);
    }
}
