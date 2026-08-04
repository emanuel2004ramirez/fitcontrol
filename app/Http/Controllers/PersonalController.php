<?php

namespace App\Http\Controllers;

use App\Http\Requests\Personal\AsignarCargoPersonalRequest;
use App\Http\Requests\Personal\CambiarEstadoPersonalRequest;
use App\Http\Requests\Personal\DeletePersonalRequest;
use App\Http\Requests\Personal\FilterPersonalRequest;
use App\Http\Requests\Personal\StoreHorarioPersonalRequest;
use App\Http\Requests\Personal\StorePersonalRequest;
use App\Http\Requests\Personal\UpdatePersonalRequest;
use App\Services\CatalogoService;
use App\Services\PersonalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PersonalController extends Controller
{
    public function __construct(
        private readonly PersonalService $service,
        private readonly CatalogoService $catalogos,
    ) {}

    public function index(FilterPersonalRequest $request): View
    {
        $filtros = $request->validated();

        return view('personal.index', [
            'personal' => $this->service->paginar($filtros, (int) ($filtros['por_pagina'] ?? 15), (int) ($filtros['page'] ?? 1)),
            'filtros' => $filtros,
            'cargos' => $this->catalogos->listar('cargos'),
            'estados' => $this->catalogos->listar('estados_personal'),
        ]);
    }

    public function create(): View
    {
        return view('personal.create', $this->catalogosFormulario());
    }

    public function store(StorePersonalRequest $request): RedirectResponse
    {
        $this->service->crear([...$request->validated(), 'usuario_id' => $request->user()?->getAuthIdentifier()]);

        return redirect()->route('personal.index')->with('success', 'Empleado registrado correctamente.');
    }

    public function show(int $personal): View
    {
        return view('personal.show', [
            'personal' => $this->service->obtener($personal),
            'historialCargos' => $this->service->historialCargos($personal),
            'historialEstados' => $this->service->historialEstados($personal),
            'horarios' => $this->service->horarios($personal),
            ...$this->catalogosFormulario(),
        ]);
    }

    public function edit(int $personal): View
    {
        return view('personal.edit', ['personal' => $this->service->obtener($personal), ...$this->catalogosFormulario()]);
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
        $this->service->cambiarEstado($personal, $data['estado_personal_id'], $data['motivo'] ?? null, $request->user()?->getAuthIdentifier());

        return back()->with('success', 'Estado actualizado correctamente.');
    }

    public function asignarCargo(AsignarCargoPersonalRequest $request, int $personal): RedirectResponse
    {
        $this->service->asignarCargo($personal, [...$request->validated(), 'usuario_id' => $request->user()?->getAuthIdentifier()]);

        return back()->with('success', 'Cargo asignado y registrado en el historial.');
    }

    public function guardarHorario(StoreHorarioPersonalRequest $request, int $personal): RedirectResponse
    {
        $this->service->guardarHorario([...$request->validated(), 'personal_id' => $personal]);

        return back()->with('success', 'Horario guardado correctamente.');
    }

    public function eliminarHorario(int $personal, int $horario): RedirectResponse
    {
        $this->service->eliminarHorario($horario);

        return back()->with('success', 'Horario finalizado correctamente.');
    }

    private function catalogosFormulario(): array
    {
        return [
            'cargos' => $this->catalogos->listar('cargos'),
            'estados' => $this->catalogos->listar('estados_personal'),
            'sexos' => $this->catalogos->listar('sexos'),
        ];
    }
}
