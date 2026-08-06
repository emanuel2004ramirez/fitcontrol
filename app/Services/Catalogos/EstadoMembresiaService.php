<?php

namespace App\Services\Catalogos;

use App\Services\StoredProcedureService;

class EstadoMembresiaService extends StoredProcedureService
{
    public function obtenerTodos(string $buscar = '', int $limite = 100, int $offset = 0): array
    {
        return $this->select('sp_estados_membresia_listar', [
            $buscar,
            $limite,
            $offset
        ]);
    }

    public function crear(array $datos): void
    {
        $this->statement('sp_estados_membresia_crear', [
            $datos['codigo'],
            $datos['nombre'],
            $datos['permite_acceso'] ?? 1,
            $datos['es_terminal'] ?? 0,
            $datos['orden'] ?? 0,
        ]);
    }

    public function actualizar(int $id, array $datos): void
    {
        $this->statement('sp_estados_membresia_actualizar', [
            $id,
            $datos['codigo'],
            $datos['nombre'],
            $datos['permite_acceso'] ?? 0,
            $datos['es_terminal'] ?? 0,
            $datos['orden'] ?? 0,
        ]);
    }

    public function eliminar(int $id): void
    {
        $this->statement('sp_estados_membresia_eliminar', [$id]);
    }
}
