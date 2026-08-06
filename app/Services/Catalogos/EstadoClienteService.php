<?php

namespace App\Services\Catalogos;

use App\Services\StoredProcedureService;

class EstadoClienteService extends StoredProcedureService
{
    public function obtenerTodos(string $buscar = '', int $limite = 100, int $offset = 0): array
    {
        return $this->select('sp_estados_cliente_listar', [
            $buscar,
            $limite,
            $offset
        ]);
    }

    public function crear(array $datos): void
    {
        $this->statement('sp_estados_cliente_crear', [
            $datos['codigo'],
            $datos['nombre'],
            $datos['activo'] ?? 1,
            $datos['es_terminal'] ?? 0,
            $datos['orden'] ?? 0,
        ]);
    }

    public function actualizar(int $id, array $datos): void
    {
        $this->statement('sp_estados_cliente_actualizar', [
            $id,
            $datos['codigo'],
            $datos['nombre'],
            $datos['activo'] ?? 0,
            $datos['es_terminal'] ?? 0,
            $datos['orden'] ?? 0,
        ]);
    }

    public function eliminar(int $id): void
    {
        $this->statement('sp_estados_cliente_eliminar', [$id]);
    }
}