<?php

namespace App\Http\Controllers;

use App\Http\Requests\Rutina\AsignarObjetivoRequest;
use App\Http\Requests\Rutina\PublicarVersionRutinaRequest;
use App\Http\Requests\Rutina\StoreEjercicioRutinaRequest;
use App\Http\Requests\Rutina\StoreRutinaRequest;
use App\Http\Requests\Rutina\StoreSesionRutinaRequest;
use App\Http\Requests\Rutina\StoreVersionRutinaRequest;
use App\Http\Requests\Rutina\UpdateRutinaRequest;
use App\Services\RutinaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RutinaController extends Controller
{
    public function __construct(private readonly RutinaService $service) {}

    public function index(): View
    {
        return view('rutinas.index', ['rutinas' => $this->service->listar()]);
    }

    public function create(): View
    {
        return view('rutinas.create');
    }

    public function store(StoreRutinaRequest $request): RedirectResponse
    {
        $this->service->crear($request->validated());

        return redirect()->route('rutinas.index')->with('success', 'Rutina creada correctamente.');
    }

    public function show(int $rutina): View
    {
        return view('rutinas.show', ['rutina' => $this->service->obtener($rutina)]);
    }

    public function edit(int $rutina): View
    {
        return view('rutinas.edit', ['rutina' => $this->service->obtener($rutina)]);
    }

    public function update(UpdateRutinaRequest $request, int $rutina): RedirectResponse
    {
        $this->service->actualizar($rutina, $request->validated());

        return redirect()->route('rutinas.show', $rutina)->with('success', 'Rutina actualizada correctamente.');
    }

    public function destroy(int $rutina): RedirectResponse
    {
        $this->service->eliminar($rutina);

        return redirect()->route('rutinas.index')->with('success', 'Rutina retirada correctamente.');
    }

    public function asignarObjetivo(AsignarObjetivoRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->service->asignarObjetivo($data['rutina_id'], $data['objetivo_id']);

        return back()->with('success', 'Objetivo asignado correctamente.');
    }

    public function crearVersion(StoreVersionRutinaRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->service->crearVersion($data['rutina_id'], $data['notas_cambio'] ?? null, $data['usuario_id'] ?? null);

        return back()->with('success', 'Versión creada correctamente.');
    }

    public function publicarVersion(PublicarVersionRutinaRequest $request): RedirectResponse
    {
        $this->service->publicarVersion($request->integer('version_rutina_id'));

        return back()->with('success', 'Versión publicada correctamente.');
    }

    public function agregarSesion(StoreSesionRutinaRequest $request): RedirectResponse
    {
        $this->service->agregarSesion($request->validated());

        return back()->with('success', 'Sesión agregada correctamente.');
    }

    public function agregarEjercicio(StoreEjercicioRutinaRequest $request): RedirectResponse
    {
        $this->service->agregarEjercicio($request->validated());

        return back()->with('success','Ejercicio agregado correctamente.');
    }
}
