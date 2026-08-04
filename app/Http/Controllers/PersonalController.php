<?php

namespace App\Http\Controllers;

use App\Http\Requests\Personal\CambiarEstadoPersonalRequest;
use App\Http\Requests\Personal\DeletePersonalRequest;
use App\Http\Requests\Personal\StoreHorarioPersonalRequest;
use App\Http\Requests\Personal\StorePersonalRequest;
use App\Http\Requests\Personal\UpdatePersonalRequest;
use App\Services\PersonalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PersonalController extends Controller
{
    public function __construct(private readonly PersonalService $service) {}

    public function index(): View
    {
        return view('personal.index', ['personal' => $this->service->listar()]);
    }

    public function create(): View
    {
        return view('personal.create');
    }

    public function store(StorePersonalRequest $request): RedirectResponse
    {
        $this->service->crear($request->validated());

        return redirect()->route('personal.index')->with('success', 'Empleado registrado correctamente.');
    }

    public function show(int $personal): View
    {
        return view('personal.show', ['personal' => $this->service->obtener($personal)]);
    }

    public function edit(int $personal): View
    {
        return view('personal.edit', ['personal' => $this->service->obtener($personal)]);
    }

    public function update(UpdatePersonalRequest $request, int $personal): RedirectResponse
    {
        $this->service->actualizar($personal, $request->validated());

        return redirect()->route('personal.show', $personal)->with('success', 'Empleado actualizado correctamente.');
    }

    public function destroy(DeletePersonalRequest $request, int $personal): RedirectResponse
    {
        $data = $request->validated();
        $this->service->eliminar($personal, $data['fecha_terminacion'] ?? null, $data['motivo']);

        return redirect()->route('personal.index')->with('success', 'Empleado retirado correctamente.');
    }

    public function cambiarEstado(CambiarEstadoPersonalRequest $request, int $personal): RedirectResponse
    {
        $data = $request->validated();
        $this->service->cambiarEstado($personal, $data['estado_personal_id'], $data['motivo'] ?? null, $data['usuario_id'] ?? null);

        return back()->with('success', 'Estado actualizado correctamente.');
    }

    public function guardarHorario(StoreHorarioPersonalRequest $request): RedirectResponse
    {
        $this->service->guardarHorario($request->validated());

        return back()->with('success', 'Horario guardado correctamente.');
    }

    public function eliminarHorario(int $horario): RedirectResponse
    {
        $this->service->eliminarHorario($horario);

        return back()->with('success','Horario eliminado correctamente.');
    }
}
