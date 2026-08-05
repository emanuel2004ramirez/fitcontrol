<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auditoria\FilterAuditoriaRequest;
use App\Services\AuditoriaService;
use Illuminate\View\View;

class AuditoriaController extends Controller
{
    public function __construct(private readonly AuditoriaService $service) {}

    public function index(FilterAuditoriaRequest $request): View
    {
        $filtros = $request->validated();

        return view('auditoria.index', [
            'auditorias' => $this->service->paginar($filtros, (int) ($filtros['por_pagina'] ?? 25), max(1, $request->integer('page', 1))),
            'filtros' => $filtros,
        ]);
    }

    public function show(int $auditoria): View
    {
        return view('auditoria.show', ['auditoria' => $this->service->obtener($auditoria)]);
    }
}
