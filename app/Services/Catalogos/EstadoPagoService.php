<?php

namespace App\Services\Catalogos;

use App\Services\StoredProcedureService;

class EstadoPagoService extends StoredProcedureService
{
    public function obtenerTodos(string $buscar = '', int $limite = 100, int $offset = 0): array
    {
        return $this->select('sp_estados_pago_listar', [
            $buscar,
            $limite,
            $offset
        ]);
    }

    public function crear(array $datos): void
    {
        $this->statement('sp_estados_pago_crear', [
            $datos['codigo'],
            $datos['nombre'],
            $datos['es_terminal'] ?? 0,
            $datos['orden'] ?? 0,
        ]);
    }

    public function actualizar(int $id, array $datos): void
    {
        $this->statement('sp_estados_pago_actualizar', [
            $id,
            $datos['codigo'],
            $datos['nombre'],
            $datos['es_terminal'] ?? 0,
            $datos['orden'] ?? 0,
        ]);
    }

    public function eliminar(int $id): void
    {
        $this->statement('sp_estados_pago_eliminar', [$id]);
    }
}
