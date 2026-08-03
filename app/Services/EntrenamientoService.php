<?php

namespace App\Services;

class EntrenamientoService extends StoredProcedureService
{
    public function listar(int $limite = 100, int $offset = 0): array
    {
        return $this->select('sp_entrenamientos_realizados_listar', [$limite, $offset]);
    }

    public function obtener(int $id): ?object
    {
        return $this->selectOne('sp_entrenamientos_realizados_obtener', [$id]);
    }

    public function iniciar(int $clienteId, ?int $versionId, ?int $sesionId, ?string $inicio = null): ?object
    {
        return $this->selectOne('sp_entrenamientos_iniciar', [$clienteId, $versionId, $sesionId, $inicio]);
    }

    public function registrarSerie(array $data): bool
    {
        return $this->statement('sp_entrenamientos_registrar_serie', [$data['entrenamiento_realizado_id'], $data['ejercicio_rutina_id'] ?? null, $data['ejercicio_id'], $data['numero_serie'], $data['repeticiones'] ?? null, $data['peso'] ?? null, $data['duracion_segundos'] ?? null, $data['distancia'] ?? null, $data['rpe'] ?? null, $data['notas'] ?? null]);
    }

    public function finalizar(int $id, ?string $fin, ?int $esfuerzo, ?string $notas): bool
    {
        return $this->statement('sp_entrenamientos_finalizar', [$id, $fin, $esfuerzo, $notas]);
    }
}
