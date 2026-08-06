<?php

namespace App\Http\Controllers;

use App\Http\Requests\Asistencia\FilterAsistenciaRequest;
use App\Http\Requests\Asistencia\RegistrarEntradaRequest;
use App\Http\Requests\Asistencia\RegistrarSalidaRequest;
use App\Services\AsistenciaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AsistenciaController extends Controller
{
    public function __construct(private readonly AsistenciaService $service) {}

    public function index(FilterAsistenciaRequest $r): View
    {
        $f = $r->validated();
        $f['desde'] = now()->startOfDay()->toDateTimeString();
        $f['hasta'] = now()->endOfDay()->toDateTimeString();

        return view('asistencias.index', ['asistencias' => $this->service->paginar($f, (int) ($f['por_pagina'] ?? 15), (int) ($f['page'] ?? 1)), 'resumen' => $this->service->resumen(), 'filtros' => $f]);
    }

    public function create(): View
    {
        return view('asistencias.create', ['clientes' => $this->service->clientesAcceso()]);
    }

    public function store(RegistrarEntradaRequest $r): RedirectResponse
    {
        $this->service->registrarEntrada([...$r->validated(), 'usuario_id' => $r->user()?->getAuthIdentifier()]);

        return redirect()->route('asistencias.index')->with('success', 'Entrada registrada.');
    }

    public function show(int $asistencia): View
    {
        return view('asistencias.show', ['asistencia' => $this->service->obtener($asistencia)]);
    }

    public function registrarSalida(RegistrarSalidaRequest $r, int $asistencia): RedirectResponse
    {
        $this->service->registrarSalida($asistencia, $r->validated('salida_at'), $r->user()?->getAuthIdentifier());

        return back()->with('success', 'Salida registrada.');
    }
}
