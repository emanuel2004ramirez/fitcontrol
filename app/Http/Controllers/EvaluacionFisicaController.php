<?php

namespace App\Http\Controllers;

use App\Http\Requests\Evaluacion\StoreEvaluacionFisicaRequest;
use App\Http\Requests\Evaluacion\StoreMedidaEvaluacionRequest;
use App\Services\EvaluacionFisicaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EvaluacionFisicaController extends Controller
{
    public function __construct(private readonly EvaluacionFisicaService $service) {}

    public function index(): View
    {
        return view('evaluaciones.index', ['evaluaciones' => $this->service->listar()]);
    }

    public function create(): View
    {
        return view('evaluaciones.create');
    }

    public function store(StoreEvaluacionFisicaRequest $request): RedirectResponse
    {
        $this->service->crear($request->validated());

        return redirect()->route('evaluaciones.index')->with('success', 'Evaluación creada correctamente.');
    }

    public function show(int $evaluacion): View
    {
        return view('evaluaciones.show', ['evaluacion' => $this->service->obtener($evaluacion)]);
    }

    public function agregarMedida(StoreMedidaEvaluacionRequest $request): RedirectResponse
    {
        $this->service->agregarMedida($request->validated());

        return back()->with('success', 'Medida agregada correctamente.');
    }

    public function destroy(int $evaluacion): RedirectResponse
    {
        $this->service->eliminar($evaluacion);

        return redirect()->route('evaluaciones.index')->with('success', 'Evaluación retirada correctamente.');
    }
}
