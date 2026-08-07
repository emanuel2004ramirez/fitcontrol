<?php

namespace App\Http\Controllers;

use App\Http\Requests\Reporte\GenerarReporteRequest;
use App\Services\ReporteExportService;
use App\Services\ReporteService;
use App\Support\Reports\ReportDefinition;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReporteController extends Controller
{
    public function __construct(private readonly ReporteService $service, private readonly ReporteExportService $exporter) {}

    public function index(): View
    {
        return view('reportes.index', ['grupos' => ReportDefinition::grouped()]);
    }

    public function show(GenerarReporteRequest $r, string $tipo): View
    {
        abort_unless(ReportDefinition::allowed($tipo), 404);

        return $this->view($tipo, $r->validated());
    }

    public function imprimir(GenerarReporteRequest $r, string $tipo): View
    {
        abort_unless(ReportDefinition::allowed($tipo), 404);

        return $this->view($tipo, $r->validated(), true);
    }

    public function pdf(GenerarReporteRequest $r, string $tipo): Response
    {
        abort_unless(ReportDefinition::allowed($tipo), 404);
        $f = $r->validated();

        return $this->exporter->pdf($tipo, $f, $this->service->generar($tipo, $f));
    }

    public function excel(GenerarReporteRequest $r, string $tipo): StreamedResponse
    {
        abort_unless(ReportDefinition::allowed($tipo), 404);

        return $this->exporter->excel($tipo, $this->service->generar($tipo, $r->validated()));
    }

    private function view(string $tipo, array $f, bool $imprimir = false): View
    {
        $resultados = $imprimir
            ? $this->service->generar($tipo, $f)
            : $this->service->paginar($tipo, $f, (int) ($f['por_pagina'] ?? 25), (int) ($f['page'] ?? 1));

        return view('reportes.show', ['tipo' => $tipo, 'definition' => ReportDefinition::get($tipo), 'filtros' => $f, 'resultados' => $resultados, 'resumen' => $this->service->resumen($tipo, $f), 'estados' => $this->service->estados($tipo), 'imprimir' => $imprimir]);
    }
}
