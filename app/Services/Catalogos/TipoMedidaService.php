<?php

namespace App\Services\Catalogos;

use App\Services\StoredProcedureService;

class TipoMedidaService extends StoredProcedureService
{
    public function obtenerTodos(string $buscar = '', int $limite = 100, int $offset = 0): array
    {
        return $this->select('sp_tipos_medida_listar', [
            $buscar,
            $limite,
            $offset
        ]);
    }

    public function crear(array $datos): void
    {
        $this->statement('sp_tipos_medida_crear', [
            $datos['codigo'],
            $datos['nombre'],
            $datos['unidad'],
            $datos['valor_minimo'] ?? null,
            $datos['valor_maximo'] ?? null,
            $datos['decimales'] ?? 0,
            $datos['activo'] ?? 1,
        ]);
    }

    public function actualizar(int $id, array $datos): void
    {
        $this->statement('sp_tipos_medida_actualizar', [
            $id,
            $datos['codigo'],
            $datos['nombre'],
            $datos['unidad'],
            $datos['valor_minimo'] ?? null,
            $datos['valor_maximo'] ?? null,
            $datos['decimales'] ?? 0,
            $datos['activo'] ?? 0,
        ]);
    }

    public function eliminar(int $id): void
    {
        $this->statement('sp_tipos_medida_eliminar', [$id]);
    }
}
