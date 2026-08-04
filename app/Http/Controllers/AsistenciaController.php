<?php

namespace App\Http\Controllers;

use App\Http\Requests\Asistencia\RegistrarEntradaRequest;
use App\Http\Requests\Asistencia\RegistrarSalidaRequest;
use App\Services\AsistenciaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AsistenciaController extends Controller
{
    public function __construct(private readonly AsistenciaService $service) {}

    public function index(): View
    {
        return view('asistencias.index', ['asistencias' => $this->service->listar()]);
    }

    public function create(): View
    {
        return view('asistencias.create');
    }

    public function store(RegistrarEntradaRequest $request): RedirectResponse
    {
        $this->service->registrarEntrada($request->validated());

        return redirect()->route('asistencias.index')->with('success', 'Entrada registrada correctamente.');
    }

    public function show(int $asistencia): View
    {
        return view('asistencias.show', ['asistencia' => $this->service->obtener($asistencia)]);
    }

    public function registrarSalida(RegistrarSalidaRequest $request, int $asistencia): RedirectResponse
    {
        $data = $request->validated();
        $this->service->registrarSalida($asistencia, $data['salida_at'] ?? null, $data['usuario_id'] ?? null);

        return back()->with('success', 'Salida registrada correctamente.');
    }
}
