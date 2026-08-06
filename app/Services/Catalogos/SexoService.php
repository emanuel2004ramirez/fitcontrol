<?php

namespace App\Services\Catalogos;

use App\Services\StoredProcedureService;

class SexoService extends StoredProcedureService
{
    public function obtenerTodos(string $buscar = '', int $limite = 100, int $offset = 0): array
    {
        return $this->select('sp_sexos_listar', [
            $buscar,
            $limite,
            $offset
        ]);
    }

    public function crear(array $datos): void
    {
        $this->statement('sp_sexos_crear', [
            $datos['codigo'],
            $datos['nombre'],
            $datos['activo'] ?? 1,
        ]);
    }

    public function actualizar(int $id, array $datos): void
    {
        $this->statement('sp_sexos_actualizar', [
            $id,
            $datos['codigo'],
            $datos['nombre'],
            $datos['activo'] ?? 0,
        ]);
    }

    public function eliminar(int $id): void
    {
        $this->statement('sp_sexos_eliminar', [
            $id,
        ]);
    }
}