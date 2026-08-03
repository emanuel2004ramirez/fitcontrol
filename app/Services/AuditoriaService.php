<?php

namespace App\Services;

class AuditoriaService extends StoredProcedureService
{
    public function listar(int $limite = 100, int $offset = 0): array
    {
        return $this->select('sp_auditoria_listar', [$limite, $offset]);
    }

    public function obtener(int $id): ?object
    {
        return $this->selectOne('sp_auditoria_obtener', [$id]);
    }

    public function registrar(array $data): bool
    {
        return $this->statement('sp_auditoria_registrar', [$data['user_id'] ?? null, $data['evento'], $data['entidad'], $data['entidad_id'] ?? null, isset($data['valores_anteriores']) ? json_encode($data['valores_anteriores'], JSON_THROW_ON_ERROR) : null, isset($data['valores_nuevos']) ? json_encode($data['valores_nuevos'], JSON_THROW_ON_ERROR) : null, $data['motivo'] ?? null, $data['ip'] ?? null, $data['user_agent'] ?? null]);
    }
}
