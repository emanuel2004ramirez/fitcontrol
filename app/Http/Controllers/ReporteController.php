<?php

namespace App\Http\Controllers;

use App\Http\Requests\Reporte\CuentasPorCobrarRequest;
use App\Http\Requests\Reporte\DiasRequest;
use App\Http\Requests\Reporte\EntrenamientosClienteRequest;
use App\Http\Requests\Reporte\PeriodoRequest;
use App\Http\Requests\Reporte\ProgresoClienteRequest;
use App\Services\ReporteService;
use Illuminate\View\View;

class ReporteController extends Controller
{
    public function __construct(private readonly ReporteService $service) {}

    public function dashboard(): View
    {
        return view('reportes.dashboard', ['resumen' => $this->service->dashboard()]);
    }

    public function membresiasPorVencer(DiasRequest $request): View
    {
        return view('reportes.membresias-por-vencer', ['resultados' => $this->service->membresiasPorVencer($request->integer('dias', 30))]);
    }

    public function membresiasVencidas(): View
    {
        return view('reportes.membresias-vencidas', ['resultados' => $this->service->membresiasVencidas()]);
    }

    public function ingresos(PeriodoRequest $request): View
    {
        $data = $request->validated();

        return view('reportes.ingresos', ['resultados' => $this->service->ingresos($data['desde'], $data['hasta'])]);
    }

    public function cuentasPorCobrar(CuentasPorCobrarRequest $request): View
    {
        return view('reportes.cuentas-por-cobrar', ['resultados' => $this->service->cuentasPorCobrar($request->integer('cliente_id') ?: null)]);
    }

    public function asistenciaDiaria(PeriodoRequest $request): View
    {
        $data = $request->validated();

        return view('reportes.asistencia-diaria', ['resultados' => $this->service->asistenciaDiaria($data['desde'], $data['hasta'])]);
    }

    public function clientesSinAsistencia(DiasRequest $request): View
    {
        return view('reportes.clientes-sin-asistencia', ['resultados' => $this->service->clientesSinAsistencia($request->integer('dias', 30))]);
    }

    public function progresoCliente(ProgresoClienteRequest $request): View
    {
        $data = $request->validated();

        return view('reportes.progreso-cliente', ['resultados' => $this->service->progresoCliente($data['cliente_id'], $data['tipo_medida_id'], $data['desde'] ?? null, $data['hasta'] ?? null)]);
    }

    public function entrenamientosCliente(EntrenamientosClienteRequest $request): View
    {
        $data = $request->validated();

        return view('reportes.entrenamientos-cliente', ['resultados' => $this->service->entrenamientosCliente($data['cliente_id'], $data['desde'], $data['hasta'])]);
    }
}
