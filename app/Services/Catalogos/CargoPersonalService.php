<?php

namespace App\Services\Catalogos;

use App\Services\StoredProcedureService;

class CargoPersonalService extends StoredProcedureService
{
    public function obtenerTodos(string $buscar = '', int $limite = 100, int $offset = 0): array
    {
        return $this->select('sp_cargos_listar', [
            $buscar,
            $limite,
            $offset
        ]);
    }

    public function crear(array $datos): void
    {
        $this->statement('sp_cargos_crear', [
            $datos['codigo'],
            $datos['nombre'],
            $datos['descripcion'] ?? null,
            $datos['activo'] ?? 1,
        ]);
    }

    public function actualizar(int $id, array $datos): void
    {
        $this->statement('sp_cargos_actualizar', [
            $id,
            $datos['codigo'],
            $datos['nombre'],
            $datos['descripcion'] ?? null,
            $datos['activo'] ?? 0,
        ]);
    }

    public function eliminar(int $id): void
    {
        $this->statement('sp_cargos_eliminar', [$id]);
    }
}
