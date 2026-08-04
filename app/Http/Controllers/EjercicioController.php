<?php

namespace App\Http\Controllers;

use App\Http\Requests\Ejercicio\AsignarGrupoMuscularRequest;
use App\Http\Requests\Ejercicio\CambiarEstadoEjercicioRequest;
use App\Http\Requests\Ejercicio\FilterEjercicioRequest;
use App\Http\Requests\Ejercicio\StoreEjercicioRequest;
use App\Http\Requests\Ejercicio\UpdateEjercicioRequest;
use App\Services\CatalogoService;
use App\Services\EjercicioService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EjercicioController extends Controller
{
    public function __construct(private readonly EjercicioService $service, private readonly CatalogoService $catalogos) {}

    public function index(FilterEjercicioRequest $r): View
    {
        $f = $r->validated();

        return view('ejercicios.index', ['ejercicios' => $this->service->paginar($f, (int) ($f['por_pagina'] ?? 15), (int) ($f['page'] ?? 1)), 'filtros' => $f, 'patrones' => $this->service->patrones(), ...$this->opciones()]);
    }

    public function create(): View
    {
        return view('ejercicios.create', $this->opciones());
    }

    public function store(StoreEjercicioRequest $r): RedirectResponse
    {
        $e = $this->service->crear($r->validated());

        return redirect()->route('ejercicios.show', $e->id)->with('success', 'Ejercicio creado.');
    }

    public function show(int $ejercicio): View
    {
        return view('ejercicios.show', ['ejercicio' => $this->service->obtener($ejercicio), 'asignados' => $this->service->grupos($ejercicio), ...$this->opciones()]);
    }

    public function edit(int $ejercicio): View
    {
        return view('ejercicios.edit', ['ejercicio' => $this->service->obtener($ejercicio), ...$this->opciones()]);
    }

    public function update(UpdateEjercicioRequest $r, int $ejercicio): RedirectResponse
    {
        $this->service->actualizar($ejercicio, $r->validated());

        return redirect()->route('ejercicios.show', $ejercicio)->with('success', 'Ejercicio actualizado.');
    }

    public function destroy(int $ejercicio): RedirectResponse
    {
        $this->service->eliminar($ejercicio);

        return redirect()->route('ejercicios.index')->with('success', 'Ejercicio retirado.');
    }

    public function asignarGrupo(AsignarGrupoMuscularRequest $r, int $ejercicio): RedirectResponse
    {
        $d = $r->validated();
        $this->service->asignarGrupoMuscular($ejercicio, $d['grupo_muscular_id'], $d['es_principal'] ?? false);

        return back()->with('success', 'Grupo asignado.');
    }

    public function retirarGrupo(int $ejercicio, int $grupo): RedirectResponse
    {
        $this->service->retirarGrupoMuscular($ejercicio, $grupo);

        return back()->with('success', 'Grupo retirado.');
    }

    public function cambiarEstado(CambiarEstadoEjercicioRequest $r, int $ejercicio): RedirectResponse
    {
        $this->service->cambiarEstado($ejercicio, $r->validated('estado_ejercicio_id'));

        return back()->with('success', 'Estado actualizado.');
    }

    private function opciones(): array
    {
        return ['estados' => $this->catalogos->listar('estados_ejercicio'), 'grupos' => $this->catalogos->listar('grupos_musculares')];
    }
}
