<?php

namespace App\Http\Controllers;

use App\Http\Requests\Rutina\ActivarVersionRutinaRequest;
use App\Http\Requests\Rutina\DuplicarRutinaRequest;
use App\Http\Requests\Rutina\FilterRutinaRequest;
use App\Http\Requests\Rutina\PublicarVersionRutinaRequest;
use App\Http\Requests\Rutina\StoreEjercicioRutinaRequest;
use App\Http\Requests\Rutina\StoreRutinaRequest;
use App\Http\Requests\Rutina\StoreSesionRutinaRequest;
use App\Http\Requests\Rutina\StoreVersionRutinaRequest;
use App\Services\CatalogoService;
use App\Services\RutinaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RutinaController extends Controller
{
    public function __construct(private readonly RutinaService $service, private readonly CatalogoService $catalogos) {}

    public function index(FilterRutinaRequest $r): View
    {
        $f = $r->validated();

        return view('rutinas.index', ['rutinas' => $this->service->paginar($f, (int) ($f['por_pagina'] ?? 15), (int) ($f['page'] ?? 1)), 'filtros' => $f, ...$this->opciones()]);
    }

    public function create(): View
    {
        return view('rutinas.create', $this->opciones());
    }

    public function store(StoreRutinaRequest $r): RedirectResponse
    {
        $x = $this->service->crear([...$r->validated(), 'usuario_id' => $r->user()?->getAuthIdentifier()]);

        return redirect()->route('rutinas.show', $x->id)->with('success', 'Rutina creada.');
    }

    public function show(int $rutina, ?int $version = null): View
    {
        $versiones = $this->service->versiones($rutina);
        $seleccion = $version ?? ($this->service->obtener($rutina)?->version_activa_id ?? ($versiones[0]->id ?? null));

        return view('rutinas.show', ['rutina' => $this->service->obtener($rutina), 'versiones' => $versiones, 'versionSeleccionada' => $seleccion, 'contenido' => $seleccion ? $this->service->contenido($seleccion) : [], 'historial' => $this->service->historial($rutina), ...$this->opciones()]);
    }

    public function crearVersion(StoreVersionRutinaRequest $r, int $rutina): RedirectResponse
    {
        $this->service->crearVersion($rutina, $r->validated('notas_cambio'), $r->user()?->getAuthIdentifier());

        return back()->with('success', 'Versión creada.');
    }

    public function publicarVersion(PublicarVersionRutinaRequest $r, int $rutina): RedirectResponse
    {
        $this->service->publicarVersion($r->integer('version_rutina_id'));

        return back()->with('success', 'Versión publicada.');
    }

    public function activarVersion(ActivarVersionRutinaRequest $r, int $rutina): RedirectResponse
    {
        $d = $r->validated();
        $this->service->activarVersion($rutina, $d['version_rutina_id'], $d['motivo'], $r->user()?->getAuthIdentifier());

        return back()->with('success', 'Versión activada.');
    }

    public function duplicar(DuplicarRutinaRequest $r, int $rutina): RedirectResponse
    {
        $x = $this->service->duplicar($rutina, [...$r->validated(), 'usuario_id' => $r->user()?->getAuthIdentifier()]);

        return redirect()->route('rutinas.show', $x->id)->with('success', 'Rutina duplicada.');
    }

    public function agregarSesion(StoreSesionRutinaRequest $r, int $rutina): RedirectResponse
    {
        $this->service->agregarSesion($r->validated());

        return back()->with('success', 'Sesión agregada.');
    }

    public function agregarEjercicio(StoreEjercicioRutinaRequest $r, int $rutina): RedirectResponse
    {
        $this->service->agregarEjercicio($r->validated());

        return back()->with('success', 'Ejercicio agregado.');
    }

    public function eliminarSesion(int $rutina, int $sesion): RedirectResponse
    {
        $this->service->eliminarSesion($sesion);

        return back()->with('success', 'Sesión eliminada.');
    }

    public function eliminarEjercicio(int $rutina, int $detalle): RedirectResponse
    {
        $this->service->eliminarEjercicio($detalle);

        return back()->with('success', 'Ejercicio retirado.');
    }

    private function opciones(): array
    {
        return ['clientes' => $this->service->clientes(), 'entrenadores' => $this->service->entrenadores(), 'ejercicios' => $this->service->ejercicios(), 'estados' => $this->catalogos->listar('estados_rutina')];
    }
}
