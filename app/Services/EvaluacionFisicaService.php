<?php

namespace App\Services;

class EvaluacionFisicaService extends StoredProcedureService
{
    public function listar(int $limite = 100, int $offset = 0): array
    {
        return $this->select('sp_evaluaciones_fisicas_listar', [$limite, $offset]);
    }

    public function obtener(int $id): ?object
    {
        return $this->selectOne('sp_evaluaciones_fisicas_obtener', [$id]);
    }

    public function crear(array $data): ?object
    {
        return $this->selectOne('sp_evaluaciones_crear', [$data['cliente_id'], $data['evaluador_id'], $data['evaluada_at'], $data['metodo'] ?? null, $data['observaciones'] ?? null, $data['usuario_id'] ?? null]);
    }

    public function agregarMedida(array $data): bool
    {
        return $this->statement('sp_evaluaciones_agregar_medida', [$data['evaluacion_fisica_id'], $data['tipo_medida_id'], $data['valor'], $data['instrumento'] ?? null, $data['observaciones'] ?? null]);
    }

    public function eliminar(int $id): bool
    {
        return $this->statement('sp_evaluaciones_eliminar', [$id]);
    }
}
