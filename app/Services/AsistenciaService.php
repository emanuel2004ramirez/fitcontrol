<?php

namespace App\Services;

class AsistenciaService extends StoredProcedureService
{
    public function listar(int $limite = 100, int $offset = 0): array
    {
        return $this->select('sp_asistencias_listar', [$limite, $offset]);
    }

    public function obtener(int $id): ?object
    {
        return $this->selectOne('sp_asistencias_obtener', [$id]);
    }

    public function buscar(?int $clienteId = null, ?string $desde = null, ?string $hasta = null, bool $soloAbiertas = false): array
    {
        return $this->select('sp_asistencias_buscar', [$clienteId, $desde, $hasta, $soloAbiertas]);
    }

    public function registrarEntrada(array $data): ?object
    {
        return $this->selectOne('sp_asistencias_registrar_entrada', [$data['cliente_id'], $data['membresia_id'], $data['entrada_at'] ?? null, $data['metodo_registro'] ?? 'MANUAL', $data['usuario_id'] ?? null, $data['observaciones'] ?? null]);
    }

    public function registrarSalida(int $asistenciaId, ?string $salidaAt, ?int $usuarioId): bool
    {
        return $this->statement('sp_asistencias_registrar_salida', [$asistenciaId, $salidaAt, $usuarioId]);
    }
}
