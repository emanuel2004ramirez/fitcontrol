<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private readonly DashboardService $service) {}

    public function index(): View
    {
        return view('dashboard.index', [
            'indicadores' => $this->service->indicadores(),
            'asistencias' => $this->service->asistencias(),
            'ingresos' => $this->service->ingresos(),
            'membresiasPlanes' => $this->service->membresiasPorPlan(),
            'actividad' => $this->service->actividad(),
            'alertas' => $this->service->alertas(),
        ]);
    }
}
