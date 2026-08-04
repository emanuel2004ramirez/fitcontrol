<?php

namespace App\Http\Controllers;

use App\Http\Requests\Entrenamiento\FinalizarEntrenamientoRequest;
use App\Http\Requests\Entrenamiento\IniciarEntrenamientoRequest;
use App\Http\Requests\Entrenamiento\StoreSerieRealizadaRequest;
use App\Services\EntrenamientoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EntrenamientoController extends Controller
{
    public function __construct(private readonly EntrenamientoService $service) {}

    public function index(): View
    {
        return view('entrenamientos.index', ['entrenamientos' => $this->service->listar()]);
    }

    public function create(): View
    {
        return view('entrenamientos.create');
    }

    public function store(IniciarEntrenamientoRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $entrenamiento = $this->service->iniciar($data['cliente_id'], $data['version_rutina_id'] ?? null, $data['sesion_rutina_id'] ?? null, $data['iniciado_at'] ?? null);

        return redirect()->route('entrenamientos.show', $entrenamiento?->id)->with('success', 'Entrenamiento iniciado correctamente.');
    }

    public function show(int $entrenamiento): View
    {
        return view('entrenamientos.show', ['entrenamiento' => $this->service->obtener($entrenamiento)]);
    }

    public function registrarSerie(StoreSerieRealizadaRequest $request): RedirectResponse
    {
        $this->service->registrarSerie($request->validated());

        return back()->with('success', 'Serie registrada correctamente.');
    }

    public function finalizar(FinalizarEntrenamientoRequest $request, int $entrenamiento): RedirectResponse
    {
        $data = $request->validated();
        $this->service->finalizar($entrenamiento, $data['finalizado_at'] ?? null, $data['esfuerzo_percibido'] ?? null, $data['notas'] ?? null);

        return back()->with('success','Entrenamiento finalizado correctamente.');
    }
}
