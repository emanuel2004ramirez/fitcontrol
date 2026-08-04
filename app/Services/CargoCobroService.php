<?php

namespace App\Services;

use Illuminate\Pagination\LengthAwarePaginator;

class CargoCobroService extends StoredProcedureService
{
    public function paginar(array $f, int $per, int $page): LengthAwarePaginator
    {
        $p = [$f['texto'] ?? null, $f['cliente_id'] ?? null, $f['estado_id'] ?? null, $f['vencidos'] ?? false, $f['desde'] ?? null, $f['hasta'] ?? null];
        $total = (int) ($this->selectOne('sp_cargos_cobro_contar', $p)?->total ?? 0);

        return new LengthAwarePaginator($this->select('sp_cargos_cobro_filtrar', [...$p, $per, ($page - 1) * $per]), $total, $per, $page, ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $f]);
    }

    public function obtener(int $id): ?object
    {
        return $this->selectOne('sp_cargos_cobro_obtener', [$id]);
    }

    public function crear(array $d): ?object
    {
        return $this->selectOne('sp_cargos_cobro_crear', [$d['numero_cargo'], $d['cliente_id'], $d['membresia_id'] ?? null, $d['estado_cargo_cobro_id'], $d['concepto'], $d['descripcion'] ?? null, $d['subtotal'], $d['descuento'] ?? 0, $d['impuesto'] ?? 0, $d['moneda'], $d['fecha_vencimiento'] ?? null, $d['usuario_id'] ?? null]);
    }

    public function actualizar(int $id, array $d): ?object
    {
        return $this->selectOne('sp_cargos_cobro_actualizar', [$id, $d['concepto'], $d['descripcion'] ?? null, $d['subtotal'], $d['descuento'] ?? 0, $d['impuesto'] ?? 0, $d['fecha_vencimiento'] ?? null]);
    }

    public function cambiarEstado(int $id, array $d): bool
    {
        return $this->statement('sp_cargos_cobro_cambiar_estado', [$id, $d['estado_cargo_cobro_id'], $d['motivo'], $d['usuario_id'] ?? null]);
    }

    public function eliminar(int $id, string $motivo, ?int $usuario): bool
    {
        return $this->statement('sp_cargos_cobro_eliminar', [$id, $motivo, $usuario]);
    }

    public function historial(int $id): array
    {
        return $this->select('sp_cargos_cobro_historial', [$id]);
    }

    public function aplicaciones(int $id): array
    {
        return $this->select('sp_cargos_cobro_aplicaciones', [$id]);
    }

    public function resumen(): ?object
    {
        return $this->selectOne('sp_cargos_cobro_resumen');
    }

    public function clientes(): array
    {
        return $this->select('sp_cargos_cobro_clientes');
    }

    public function membresias(?int $cliente = null): array
    {
        return $this->select('sp_cargos_cobro_membresias', [$cliente]);
    }
}
