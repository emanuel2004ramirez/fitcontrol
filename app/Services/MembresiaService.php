<?php

namespace App\Services;

class MembresiaService extends StoredProcedureService
{
    public function listar(int $limite = 100, int $offset = 0): array
    {
        return $this->select('sp_membresias_listar', [$limite, $offset]);
    }

    public function obtener(int $id): ?object
    {
        return $this->selectOne('sp_membresias_obtener', [$id]);
    }

    public function buscar(?int $clienteId = null, ?int $estadoId = null, ?string $desde = null, ?string $hasta = null): array
    {
        return $this->select('sp_membresias_buscar', [$clienteId, $estadoId, $desde, $hasta]);
    }

    public function listarPrecios(int $limite = 100, int $offset = 0): array
    {
        return $this->select('sp_precios_membresia_listar', [$limite, $offset]);
    }

    public function obtenerPrecio(int $id): ?object
    {
        return $this->selectOne('sp_precios_membresia_obtener', [$id]);
    }

    public function crearPrecio(array $data): ?object
    {
        return $this->selectOne('sp_precios_membresia_crear', [$data['tipo_membresia_id'], $data['precio'], $data['moneda'], $data['vigente_desde'], $data['motivo_cambio'] ?? null]);
    }

    public function crear(array $data): ?object
    {
        return $this->selectOne('sp_membresias_crear', [$data['cliente_id'], $data['tipo_membresia_id'], $data['precio_membresia_id'], $data['estado_membresia_id'], $data['fecha_inicio'], $data['fecha_fin'], $data['origen'] ?? 'NUEVA', $data['membresia_anterior_id'] ?? null, $data['usuario_id'] ?? null]);
    }

    public function cambiarEstado(int $id, int $estadoId, bool $mantenerActiva, ?string $motivo, ?int $usuarioId): bool
    {
        return $this->statement('sp_membresias_cambiar_estado', [$id, $estadoId, $mantenerActiva, $motivo, $usuarioId]);
    }

    public function cancelar(int $id, int $estadoCanceladaId, string $motivo, ?int $usuarioId): bool
    {
        return $this->statement('sp_membresias_cancelar', [$id, $estadoCanceladaId, $motivo, $usuarioId]);
    }

    public function suspender(int $id, string $inicio, ?string $fin, int $diasExtension, string $motivo, ?int $usuarioId): bool
    {
        return $this->statement('sp_membresias_suspender', [$id, $inicio, $fin, $diasExtension, $motivo, $usuarioId]);
    }

    public function eliminar(int $id): bool
    {
        return $this->statement('sp_membresias_eliminar', [$id]);
    }
}
