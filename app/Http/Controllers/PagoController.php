<?php

namespace App\Http\Controllers;

use App\Http\Requests\Pago\CambiarEstadoPagoRequest;
use App\Http\Requests\Pago\FilterPagoRequest;
use App\Http\Requests\Pago\StorePagoRequest;
use App\Services\CatalogoService;
use App\Services\PagoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PagoController extends Controller
{
    public function __construct(private readonly PagoService $service, private readonly CatalogoService $catalogos) {}

    public function index(FilterPagoRequest $r): View
    {
        $f = $r->validated();

        return view('pagos.index', ['pagos' => $this->service->paginar($f, (int) ($f['por_pagina'] ?? 15), (int) ($f['page'] ?? 1)), 'filtros' => $f, 'resumen' => $this->service->resumen(), ...$this->opciones()]);
    }

    public function create(): View
    {
        return view('pagos.create', ['membresias' => $this->service->membresiasPendientes(), ...$this->opciones()]);
    }

    public function store(StorePagoRequest $r): RedirectResponse
    {
        $p = $this->service->registrarCompleto([...$r->validated(), 'usuario_id' => $r->user()?->getAuthIdentifier()]);

        return redirect()->route('pagos.show', $p->id)->with('success', 'Pago registrado correctamente.');
    }

    public function show(int $pago): View
    {
        return view('pagos.show', ['pago' => $this->service->obtener($pago), 'historial' => $this->service->historial($pago), 'aplicaciones' => $this->service->aplicaciones($pago), ...$this->opciones()]);
    }

    public function cambiarEstado(CambiarEstadoPagoRequest $r, int $pago): RedirectResponse
    {
        $d = $r->validated();
        $this->service->cambiarEstado($pago, $d['estado_pago_id'], $d['motivo'] ?? null, $r->user()?->getAuthIdentifier());

        return back()->with('success', 'Estado actualizado.');
    }

    private function opciones(): array
    {
        return ['estados' => $this->catalogos->listar('estados_pago'), 'metodos' => $this->catalogos->listar('metodos_pago')];
    }
}
