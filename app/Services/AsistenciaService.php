<?php

namespace App\Services;

use Illuminate\Pagination\LengthAwarePaginator;

class AsistenciaService extends StoredProcedureService
{
    public function paginar(array $f, int $per, int $page): LengthAwarePaginator
    {
        $p = [
            $f['texto'] ?? null,
            $f['cliente_id'] ?? null,
            $f['metodo'] ?? null,
            $f['solo_abiertas'] ?? 0,
            $f['desde'] ?? null,
            $f['hasta'] ?? null
        ];

        return $this->paginateProcedures('sp_asistencias_contar', 'sp_asistencias_filtrar', $p, $per, $page, $f);
    }

    public function clientesAcceso(): array
    {
        return $this->select('sp_asistencias_clientes_acceso');
    }

    public function registrarEntrada(int $clienteId, int $membresiaId, ?int $usuarioId, ?string $observaciones): void
    {
        $this->statement('sp_asistencias_registrar_entrada', [
            $clienteId, 
            $membresiaId, 
            null, 
            'MANUAL', 
            $usuarioId, 
            $observaciones
        ]);
    }

    public function registrarSalida(int $asistenciaId, ?int $usuarioId): void
    {
        $this->statement('sp_asistencias_registrar_salida', [
            $asistenciaId, 
            null, 
            $usuarioId
        ]);
    }
}
