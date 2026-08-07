<?php

namespace App\Services;

use App\Support\Reports\ReportDefinition;
use Illuminate\Pagination\LengthAwarePaginator;

class ReporteService extends StoredProcedureService
{
    public function generar(string $tipo, array $filtros): array
    {
        ReportDefinition::get($tipo);

        return $this->select('sp_reportes_generar', [$tipo, $filtros['desde'] ?? null, $filtros['hasta'] ?? null, $filtros['busqueda'] ?? null, $filtros['estado_id'] ?? null, 10000, 0]);
    }

    public function paginar(string $tipo, array $filtros, int $porPagina, int $pagina): LengthAwarePaginator
    {
        ReportDefinition::get($tipo);
        $parameters = [$tipo, $filtros['desde'] ?? null, $filtros['hasta'] ?? null, $filtros['busqueda'] ?? null, $filtros['estado_id'] ?? null];

        return $this->paginateProcedures('sp_reportes_contar', 'sp_reportes_generar', $parameters, $porPagina, $pagina, $filtros);
    }

    public function resumen(string $tipo, array $filtros): ?object
    {
        ReportDefinition::get($tipo);

        return $this->selectOne('sp_reportes_resumen', [$tipo, $filtros['desde'] ?? null, $filtros['hasta'] ?? null, $filtros['busqueda'] ?? null, $filtros['estado_id'] ?? null]);
    }

    public function estados(string $tipo): array
    {
        $catalogo = ['clientes' => 'estados_cliente', 'personal' => 'estados_personal', 'pagos' => 'estados_pago', 'membresias' => 'estados_membresia'][$tipo] ?? null;

        return $catalogo ? $this->select("sp_{$catalogo}_listar", [null, 100, 0]) : [];
    }

    public function dashboard(): ?object
    {
        return $this->selectOne('sp_dashboard_resumen');
    }

    public function membresiasPorVencer(int $dias = 30): array
    {
        return $this->select('sp_reporte_membresias_por_vencer', [$dias]);
    }

    public function membresiasVencidas(): array
    {
        return $this->select('sp_reporte_membresias_vencidas');
    }

    public function ingresos(string $desde, string $hasta): array
    {
        return $this->select('sp_reporte_ingresos', [$desde, $hasta]);
    }

    public function asistenciaDiaria(string $desde, string $hasta): array
    {
        return $this->select('sp_reporte_asistencia_diaria', [$desde, $hasta]);
    }

    public function clientesSinAsistencia(int $dias = 30): array
    {
        return $this->select('sp_reporte_clientes_sin_asistencia', [$dias]);
    }

    public function progresoCliente(int $clienteId, int $tipoMedidaId, ?string $desde = null, ?string $hasta = null): array
    {
        return $this->select('sp_reporte_progreso_cliente', [$clienteId, $tipoMedidaId, $desde, $hasta]);
    }

    public function entrenamientosCliente(int $clienteId, string $desde, string $hasta): array
    {
        return $this->select('sp_reporte_entrenamientos_cliente', [$clienteId, $desde, $hasta]);
    }
}
