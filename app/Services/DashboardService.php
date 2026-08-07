<?php

namespace App\Services;

class DashboardService extends StoredProcedureService
{
    public function indicadores(): ?object
    {
        return $this->selectOne('sp_dashboard_indicadores');
    }

    public function asistencias(int $dias = 14): array
    {
        return $this->select('sp_dashboard_asistencias_serie', [$dias]);
    }

    public function ingresos(int $dias = 14): array
    {
        return $this->select('sp_dashboard_ingresos_serie', [$dias]);
    }

    public function membresiasPorPlan(): array
    {
        return $this->select('sp_dashboard_membresias_planes');
    }

    public function actividad(int $limite = 8): array
    {
        return $this->select('sp_dashboard_actividad_reciente', [$limite]);
    }

    public function alertas(int $limite = 6): array
    {
        return $this->select('sp_dashboard_alertas', [$limite]);
    }
}
