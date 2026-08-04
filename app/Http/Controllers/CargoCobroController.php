<?php

namespace App\Http\Controllers;

use App\Http\Requests\CargoCobro\CambiarEstadoCargoCobroRequest;
use App\Http\Requests\CargoCobro\DeleteCargoCobroRequest;
use App\Http\Requests\CargoCobro\FilterCargoCobroRequest;
use App\Http\Requests\CargoCobro\StoreCargoCobroRequest;
use App\Http\Requests\CargoCobro\UpdateCargoCobroRequest;
use App\Services\CargoCobroService;
use App\Services\CatalogoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CargoCobroController extends Controller
{
    public function __construct(private readonly CargoCobroService $service, private readonly CatalogoService $catalogos) {}

    public function index(FilterCargoCobroRequest $r): View
    {
        $f = $r->validated();

        return view('cargos-cobro.index', ['cargos' => $this->service->paginar($f, (int) ($f['por_pagina'] ?? 15), (int) ($f['page'] ?? 1)), 'resumen' => $this->service->resumen(), 'filtros' => $f, 'estados' => $this->estados(), 'clientes' => $this->service->clientes()]);
    }

    public function create(): View
    {
        return view('cargos-cobro.create', ['estados' => $this->estados(), 'clientes' => $this->service->clientes(), 'membresias' => $this->service->membresias()]);
    }

    public function store(StoreCargoCobroRequest $r): RedirectResponse
    {
        $c = $this->service->crear([...$r->validated(), 'usuario_id' => $r->user()?->getAuthIdentifier()]);

        return redirect()->route('cargos-cobro.show', $c->id)->with('success', 'Cargo creado correctamente.');
    }

    public function show(int $cargo): View
    {
        return view('cargos-cobro.show', ['cargo' => $this->service->obtener($cargo), 'historial' => $this->service->historial($cargo), 'aplicaciones' => $this->service->aplicaciones($cargo), 'estados' => $this->estados()]);
    }

    public function edit(int $cargo): View
    {
        return view('cargos-cobro.edit', ['cargo' => $this->service->obtener($cargo)]);
    }

    public function update(UpdateCargoCobroRequest $r, int $cargo): RedirectResponse
    {
        $this->service->actualizar($cargo, $r->validated());

        return redirect()->route('cargos-cobro.show', $cargo)->with('success', 'Cargo actualizado.');
    }

    public function cambiarEstado(CambiarEstadoCargoCobroRequest $r, int $cargo): RedirectResponse
    {
        $this->service->cambiarEstado($cargo, [...$r->validated(), 'usuario_id' => $r->user()?->getAuthIdentifier()]);

        return back()->with('success', 'Estado actualizado.');
    }

    public function destroy(DeleteCargoCobroRequest $r, int $cargo): RedirectResponse
    {
        $this->service->eliminar($cargo, $r->validated('motivo'), $r->user()?->getAuthIdentifier());

        return redirect()->route('cargos-cobro.index')->with('success', 'Cargo retirado.');
    }

    private function estados(): array
    {
        return $this->catalogos->listar('estados_cargo_cobro');
    }
}
