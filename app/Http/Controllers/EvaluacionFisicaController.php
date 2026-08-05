<?php

namespace App\Http\Controllers;

use App\Http\Requests\Evaluacion\CompararEvaluacionesRequest;
use App\Http\Requests\Evaluacion\FilterEvaluacionRequest;
use App\Http\Requests\Evaluacion\StoreEvaluacionFisicaRequest;
use App\Http\Requests\Evaluacion\StoreMedidaEvaluacionRequest;
use App\Services\CatalogoService;
use App\Services\EvaluacionFisicaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EvaluacionFisicaController extends Controller
{
    public function __construct(private readonly EvaluacionFisicaService $service, private readonly CatalogoService $catalogos) {}

    public function index(FilterEvaluacionRequest $r): View
    {
        $f = $r->validated();

        return view('evaluaciones.index', ['evaluaciones' => $this->service->paginar($f, (int) ($f['por_pagina'] ?? 15), (int) ($f['page'] ?? 1)), 'filtros' => $f, ...$this->opciones()]);
    }

    public function create(): View
    {
        return view('evaluaciones.create', $this->opciones());
    }

    public function store(StoreEvaluacionFisicaRequest $r): RedirectResponse
    {
        $e = $this->service->crear([...$r->validated(), 'usuario_id' => $r->user()?->getAuthIdentifier()]);

        return redirect()->route('evaluaciones.show', $e->id)->with('success', 'Evaluación registrada.');
    }

    public function show(int $evaluacion): View
    {
        $e = $this->service->obtener($evaluacion);

        return view('evaluaciones.show', ['evaluacion' => $e, 'medidas' => $this->service->medidas($evaluacion), 'historial' => $this->service->historialCliente($e->cliente_id), 'tiposMedida' => $this->catalogos->listar('tipos_medida'), 'comparacion' => []]);
    }

    public function agregarMedida(StoreMedidaEvaluacionRequest $r, int $evaluacion): RedirectResponse
    {
        $this->service->agregarMedida([...$r->validated(), 'evaluacion_fisica_id' => $evaluacion]);

        return back()->with('success', 'Medida registrada.');
    }

    public function comparar(CompararEvaluacionesRequest $r, int $evaluacion): View
    {
        $e = $this->service->obtener($evaluacion);

        return view('evaluaciones.show', ['evaluacion' => $e, 'medidas' => $this->service->medidas($evaluacion), 'historial' => $this->service->historialCliente($e->cliente_id), 'tiposMedida' => $this->catalogos->listar('tipos_medida'), 'comparacion' => $this->service->comparar($evaluacion, $r->integer('evaluacion_comparada_id'))]);
    }

    private function opciones(): array
    {
        return ['clientes' => $this->service->clientes(), 'evaluadores' => $this->service->evaluadores()];
    }
}
