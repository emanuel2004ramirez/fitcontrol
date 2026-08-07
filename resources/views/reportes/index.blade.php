@extends('layouts.app')
@section('title', 'Reportes')
@section('content')
<x-page-header title="Reportes" subtitle="Información gerencial y listados administrativos exportables." />

<section class="mb-5">
    <div class="mb-3">
        <h2 class="h5 mb-1">Análisis gerencial</h2>
        <p class="text-muted mb-0">Indicadores para supervisar la operación y tomar decisiones.</p>
    </div>
    <div class="row g-4">
        @foreach($grupos['management'] as $tipo => $reporte)
            <div class="col-md-6 col-xl-4">
                <a href="{{ route('reportes.show', $tipo) }}" class="card app-card h-100 report-card text-decoration-none">
                    <div class="card-body d-flex gap-3">
                        <span class="report-icon flex-shrink-0"><i class="bi {{ $reporte['icon'] }}"></i></span>
                        <div><h3 class="h5 mb-2">{{ $reporte['title'] }}</h3><p class="text-muted mb-0">{{ $reporte['description'] }}</p></div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
</section>

<section>
    <div class="mb-3">
        <h2 class="h5 mb-1">Listados administrativos</h2>
        <p class="text-muted mb-0">Directorios filtrables para consulta, impresión y exportación.</p>
    </div>
    <div class="row g-4">
        @foreach($grupos['administrative'] as $tipo => $reporte)
            <div class="col-md-6">
                <a href="{{ route('reportes.show', $tipo) }}" class="card app-card h-100 report-card text-decoration-none">
                    <div class="card-body d-flex gap-3">
                        <span class="report-icon flex-shrink-0"><i class="bi {{ $reporte['icon'] }}"></i></span>
                        <div><h3 class="h5 mb-2">{{ $reporte['title'] }}</h3><p class="text-muted mb-0">{{ $reporte['description'] }}</p></div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
</section>
@endsection
