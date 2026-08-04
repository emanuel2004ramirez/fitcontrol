<?php

namespace App\Services;

use Illuminate\Pagination\LengthAwarePaginator;

class PagoService extends StoredProcedureService
{
    public function paginar(array $f, int $per, int $page): LengthAwarePaginator
    {
        $p = [$f['texto'] ?? null, $f['cliente_id'] ?? null, $f['estado_id'] ?? null, $f['metodo_id'] ?? null, $f['desde'] ?? null, $f['hasta'] ?? null];
        $total = (int) ($this->selectOne('sp_pagos_contar', $p)?->total ?? 0);

        return new LengthAwarePaginator($this->select('sp_pagos_filtrar', [...$p, $per, ($page - 1) * $per]), $total, $per, $page, ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $f]);
    }

    public function historial(int $id): array
    {
        return $this->select('sp_pagos_historial', [$id]);
    }

    public function aplicaciones(int $id): array
    {
        return $this->select('sp_pagos_aplicaciones', [$id]);
    }

    public function cargosDisponibles(int $id): array
    {
        return $this->select('sp_pagos_cargos_disponibles', [$id]);
    }

    public function resumen(): ?object
    {
        return $this->selectOne('sp_pagos_resumen');
    }

    public function clientes(): array
    {
        return $this->select('sp_pagos_clientes');
    }

    public function listar(int $limite = 100, int $offset = 0): array
    {
        return $this->select('sp_pagos_listar', [$limite, $offset]);
    }

    public function obtener(int $id): ?object
    {
        return $this->selectOne('sp_pagos_obtener', [$id]);
    }

    public function listarCargos(int $limite = 100, int $offset = 0): array
    {
        return $this->select('sp_cargos_cobro_listar', [$limite, $offset]);
    }

    public function obtenerCargo(int $id): ?object
    {
        return $this->selectOne('sp_cargos_cobro_obtener', [$id]);
    }

    public function listarReembolsos(int $limite = 100, int $offset = 0): array
    {
        return $this->select('sp_reembolsos_listar', [$limite, $offset]);
    }

    public function crearCargo(array $data): ?object
    {
        return $this->selectOne('sp_cargos_cobro_crear', [$data['numero_cargo'], $data['cliente_id'], $data['membresia_id'] ?? null, $data['estado_cargo_cobro_id'], $data['concepto'], $data['descripcion'] ?? null, $data['subtotal'], $data['descuento'] ?? 0, $data['impuesto'] ?? 0, $data['moneda'], $data['fecha_vencimiento'] ?? null, $data['usuario_id'] ?? null]);
    }

    public function cambiarEstadoCargo(int $id, int $estadoId): bool
    {
        return $this->statement('sp_cargos_cobro_cambiar_estado', [$id, $estadoId]);
    }

    public function registrar(array $data): ?object
    {
        return $this->selectOne('sp_pagos_registrar', [$data['idempotency_key'], $data['numero_recibo'], $data['cliente_id'], $data['metodo_pago_id'], $data['estado_pago_id'], $data['monto'], $data['moneda'], $data['referencia'] ?? null, $data['referencia_externa'] ?? null, $data['pagado_at'] ?? null, $data['usuario_id'] ?? null, $data['observaciones'] ?? null]);
    }

    public function aplicar(int $pagoId, int $cargoId, string|float $monto): bool
    {
        return $this->statement('sp_pagos_aplicar', [$pagoId, $cargoId, $monto]);
    }

    public function cambiarEstado(int $id, int $estadoId, ?string $motivo, ?int $usuarioId): bool
    {
        return $this->statement('sp_pagos_cambiar_estado', [$id, $estadoId, $motivo, $usuarioId]);
    }

    public function reembolsar(array $data): bool
    {
        return $this->statement('sp_reembolsos_crear', [$data['numero_reembolso'], $data['pago_id'], $data['monto'], $data['motivo'], $data['referencia_externa'] ?? null, $data['usuario_id'] ?? null]);
    }
}
