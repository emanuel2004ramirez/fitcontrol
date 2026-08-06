<?php

namespace App\Services\Catalogos;

use App\Services\StoredProcedureService;

class TipoMembresiaService extends StoredProcedureService
{
    public function obtenerTodos(string $buscar = '', int $limite = 100, int $offset = 0): array
    {
        return $this->select('sp_tipos_membresia_listar', [
            $buscar,
            $limite,
            $offset
        ]);
    }

    public function crear(array $datos): void
    {
        $this->statement('sp_tipos_membresia_crear', [
            $datos['codigo'],
            $datos['nombre'],
            $datos['descripcion'] ?? null,
            $datos['duracion_dias'],
            $datos['activo'] ?? 1,
            $datos['precio'],
            $datos['moneda'],
        ]);
    }

    public function actualizar(int $id, array $datos): void
    {
        $this->statement('sp_tipos_membresia_actualizar', [
            $id,
            $datos['codigo'],
            $datos['nombre'],
            $datos['descripcion'] ?? null,
            $datos['duracion_dias'],
            $datos['activo'] ?? 0,
            $datos['precio'],
            $datos['moneda'],
        ]);
    }

    public function eliminar(int $id): void
    {
        $this->statement('sp_tipos_membresia_eliminar', [$id]);
    }
}
