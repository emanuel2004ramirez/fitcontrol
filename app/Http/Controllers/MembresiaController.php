<?php

namespace App\Http\Controllers;

use App\Http\Requests\Membresia\CambiarEstadoMembresiaRequest;
use App\Http\Requests\Membresia\CancelarMembresiaRequest;
use App\Http\Requests\Membresia\CongelarMembresiaRequest;
use App\Http\Requests\Membresia\FilterMembresiaRequest;
use App\Http\Requests\Membresia\ReactivarMembresiaRequest;
use App\Http\Requests\Membresia\RenovarMembresiaRequest;
use App\Http\Requests\Membresia\StoreMembresiaRequest;
use App\Services\CatalogoService;
use App\Services\MembresiaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MembresiaController extends Controller
{
    public function __construct(private readonly MembresiaService $service, private readonly CatalogoService $catalogos) {}

    public function index(FilterMembresiaRequest $request): View
    {
        $f = $request->validated();

        return view('membresias.index', ['membresias' => $this->service->paginar($f, (int) ($f['por_pagina'] ?? 15), (int) ($f['page'] ?? 1)), 'filtros' => $f, ...$this->catalogos()]);
    }

    public function create(): View
    {
        return view('membresias.create', ['clientes' => $this->service->clientesDisponibles(), 'precios' => $this->service->preciosDisponibles(), ...$this->catalogos()]);
    }

    public function store(StoreMembresiaRequest $request): RedirectResponse
    {
        $this->service->crear([...$request->validated(), 'usuario_id' => $request->user()?->getAuthIdentifier()]);

        return redirect()->route('membresias.index')->with('success', 'Membresía creada correctamente.');
    }

    public function show(int $membresia): View
    {
        return view('membresias.show', ['membresia' => $this->service->obtener($membresia), 'historial' => $this->service->historial($membresia), 'suspensiones' => $this->service->suspensiones($membresia), 'precios' => $this->service->preciosDisponibles(), ...$this->catalogos()]);
    }

    public function cambiarEstado(CambiarEstadoMembresiaRequest $request, int $membresia): RedirectResponse
    {
        $d = $request->validated();
        $this->service->cambiarEstado($membresia, $d['estado_membresia_id'], $d['mantener_activa'], $d['motivo'] ?? null, $request->user()?->getAuthIdentifier());

        return back()->with('success', 'Estado actualizado.');
    }

    public function cancelar(CancelarMembresiaRequest $request, int $membresia): RedirectResponse
    {
        $d = $request->validated();
        $this->service->cancelar($membresia, $d['estado_cancelada_id'], $d['motivo'], $request->user()?->getAuthIdentifier());

        return back()->with('success', 'Membresía cancelada.');
    }

    public function renovar(RenovarMembresiaRequest $request, int $membresia): RedirectResponse
    {
        $n = $this->service->renovar($membresia, [...$request->validated(), 'usuario_id' => $request->user()?->getAuthIdentifier()]);

        return redirect()->route('membresias.show', $n->id)->with('success', 'Membresía renovada correctamente.');
    }

    public function congelar(CongelarMembresiaRequest $request, int $membresia): RedirectResponse
    {
        $this->service->congelar($membresia, [...$request->validated(), 'usuario_id' => $request->user()?->getAuthIdentifier()]);

        return back()->with('success', 'Membresía congelada.');
    }

    public function reactivar(ReactivarMembresiaRequest $request, int $membresia): RedirectResponse
    {
        $this->service->reactivar($membresia, [...$request->validated(), 'usuario_id' => $request->user()?->getAuthIdentifier()]);

        return back()->with('success', 'Membresía reactivada.');
    }

    private function catalogos(): array
    {
        return ['estados' => $this->catalogos->listar('estados_membresia'), 'tipos' => $this->catalogos->listar('tipos_membresia')];
    }
}
