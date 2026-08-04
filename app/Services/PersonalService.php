<?php

namespace App\Services;

use Illuminate\Pagination\LengthAwarePaginator;

class PersonalService extends StoredProcedureService
{
    public function listar(int $limite = 100, int $offset = 0): array
    {
        return $this->select('sp_personal_listar', [$limite, $offset]);
    }

    public function obtener(int $id): ?object
    {
        return $this->selectOne('sp_personal_obtener', [$id]);
    }

    public function buscar(?string $texto = null, ?int $estadoId = null): array
    {
        return $this->select('sp_personal_buscar', [$texto, $estadoId]);
    }

    public function paginar(array $filtros, int $porPagina, int $pagina): LengthAwarePaginator
    {
        $parametros = [
            $filtros['texto'] ?? null,
            $filtros['estado_id'] ?? null,
            $filtros['cargo_id'] ?? null,
            $filtros['fecha_desde'] ?? null,
            $filtros['fecha_hasta'] ?? null,
        ];
        $total = (int) ($this->selectOne('sp_personal_contar', $parametros)?->total ?? 0);
        $resultados = $this->select('sp_personal_filtrar', [
            ...$parametros, $porPagina, ($pagina - 1) * $porPagina,
        ]);

        return new LengthAwarePaginator($resultados, $total, $porPagina, $pagina, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'query' => $filtros,
        ]);
    }

    public function crear(array $data): ?object
    {
        return $this->selectOne('sp_personal_crear', [$data['codigo_empleado'], $data['cargo_id'], $data['sexo_id'] ?? null, $data['estado_personal_id'], $data['nombre'], $data['apellido'], $data['tipo_identificacion'] ?? null, $data['numero_identificacion'] ?? null, $data['telefono'] ?? null, $data['correo_electronico'] ?? null, $data['fecha_contratacion'], $data['usuario_id'] ?? null]);
    }

    public function actualizar(int $id, array $data): ?object
    {
        return $this->selectOne('sp_personal_actualizar', [$id, $data['sexo_id'] ?? null, $data['nombre'], $data['apellido'], $data['tipo_identificacion'] ?? null, $data['numero_identificacion'] ?? null, $data['telefono'] ?? null, $data['correo_electronico'] ?? null]);
    }

    public function asignarCargo(int $id, array $data): bool
    {
        return $this->statement('sp_personal_asignar_cargo', [$id, $data['cargo_id'], $data['vigente_desde'], $data['motivo'], $data['usuario_id'] ?? null]);
    }

    public function historialCargos(int $id): array
    {
        return $this->select('sp_personal_historial_cargos', [$id]);
    }

    public function historialEstados(int $id): array
    {
        return $this->select('sp_personal_historial_estados', [$id]);
    }

    public function horarios(int $id): array
    {
        return $this->select('sp_personal_horarios', [$id]);
    }

    public function cambiarEstado(int $id, int $estadoId, ?string $motivo, ?int $usuarioId): bool
    {
        return $this->statement('sp_personal_cambiar_estado', [$id, $estadoId, $motivo, $usuarioId]);
    }

    public function eliminar(int $id, ?string $fechaTerminacion, string $motivo): bool
    {
        return $this->statement('sp_personal_eliminar', [$id, $fechaTerminacion, $motivo]);
    }

    public function guardarHorario(array $data): ?object
    {
        return $this->selectOne('sp_horarios_personal_guardar', [$data['id'] ?? null, $data['personal_id'], $data['dia_semana'], $data['hora_inicio'], $data['hora_fin'], $data['vigente_desde'], $data['vigente_hasta'] ?? null]);
    }

    public function eliminarHorario(int $id): bool
    {
        return $this->statement('sp_horarios_personal_eliminar', [$id]);
    }
}
