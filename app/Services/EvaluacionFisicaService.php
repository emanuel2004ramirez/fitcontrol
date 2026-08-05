<?php

namespace App\Services;

use Illuminate\Pagination\LengthAwarePaginator;

class EvaluacionFisicaService extends StoredProcedureService
{
    public function paginar(array $f, int $per, int $page): LengthAwarePaginator
    {
        $p = [$f['texto'] ?? null, $f['cliente_id'] ?? null, $f['evaluador_id'] ?? null, $f['desde'] ?? null, $f['hasta'] ?? null];

        return $this->paginateProcedures('sp_evaluaciones_contar', 'sp_evaluaciones_filtrar', $p, $per, $page, $f);
    }

    public function medidas(int $id): array
    {
        return $this->select('sp_evaluaciones_medidas', [$id]);
    }

    public function historialCliente(int $id): array
    {
        return $this->select('sp_evaluaciones_historial_cliente', [$id]);
    }

    public function comparar(int $base, int $comparada): array
    {
        return $this->select('sp_evaluaciones_comparar', [$base, $comparada]);
    }

    public function clientes(): array
    {
        return $this->select('sp_evaluaciones_clientes');
    }

    public function evaluadores(): array
    {
        return $this->select('sp_evaluaciones_evaluadores');
    }

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

    public function crearCompleta(array $data): ?object
    {
        return $this->selectOne('sp_evaluaciones_crear_completa', [$data['cliente_id'], $data['evaluador_id'], $data['evaluada_at'], $data['metodo'], $data['observaciones'] ?? null, $data['usuario_id'] ?? null, json_encode(array_values($data['medidas']), JSON_THROW_ON_ERROR)]);
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
