@extends('layouts.app')
@section('title', $definition['title'])
@php
    $total = $resultados instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator ? $resultados->total() : count($resultados);
    $formatValue = static function (mixed $value, ?string $format): string {
        if ($value === null || $value === '') return '—';
        return match ($format) {
            'date' => \Illuminate\Support\Carbon::parse($value)->format('d/m/Y'),
            'datetime' => \Illuminate\Support\Carbon::parse($value)->format('d/m/Y H:i'),
            'money' => number_format((float) $value, 2),
            'decimal' => rtrim(rtrim(number_format((float) $value, 4, '.', ''), '0'), '.'),
            'minutes' => ((int) $value).' min',
            default => (string) $value,
        };
    };
@endphp
@section('content')
<x-page-header :title="$definition['title']" :subtitle="$definition['description']">
    <x-button :href="route('reportes.pdf', array_merge(['tipo'=>$tipo], $filtros))" variant="danger" icon="bi-file-earmark-pdf">PDF</x-button>
    <x-button :href="route('reportes.excel', array_merge(['tipo'=>$tipo], $filtros))" variant="success" icon="bi-file-earmark-excel">Excel</x-button>
    <x-button :href="route('reportes.imprimir', array_merge(['tipo'=>$tipo], $filtros))" variant="outline-secondary" icon="bi-printer" target="_blank">Imprimir</x-button>
</x-page-header>

@if($resumen)
<div class="row g-3 mb-4">
    @foreach([1, 2, 3] as $index)
        @php($label = $resumen->{'metrica_'.$index.'_etiqueta'} ?? null)
        @if($label)
            <div class="col-md-4"><x-stat-card :label="$label" :value="$resumen->{'metrica_'.$index.'_valor'} ?? 0" :icon="['bi-list-check','bi-graph-up-arrow','bi-people'][$index-1]" :tone="['primary','success','info'][$index-1]" /></div>
        @endif
    @endforeach
</div>
@endif

<x-card title="Filtros" class="mb-4 no-print">
    <form method="GET" action="{{ route('reportes.show', $tipo) }}" class="row g-3 align-items-end">
        <div class="col-lg-4"><label class="form-label">Buscar</label><input class="form-control" name="busqueda" value="{{ $filtros['busqueda'] ?? '' }}" placeholder="{{ $definition['search'] }}"></div>
        <div class="col-sm-6 col-lg-2"><label class="form-label">{{ $definition['date_from'] }}</label><input type="date" class="form-control" name="desde" value="{{ $filtros['desde'] ?? '' }}"></div>
        <div class="col-sm-6 col-lg-2"><label class="form-label">{{ $definition['date_to'] }}</label><input type="date" class="form-control" name="hasta" value="{{ $filtros['hasta'] ?? '' }}"></div>
        @if($estados)<div class="col-sm-6 col-lg-2"><label class="form-label">Estado</label><select class="form-select" name="estado_id"><option value="">Todos</option>@foreach($estados as $estado)<option value="{{ $estado->id }}" @selected(($filtros['estado_id'] ?? null) == $estado->id)>{{ $estado->nombre }}</option>@endforeach</select></div>@endif
        <div class="col-sm-6 col-lg-2"><label class="form-label">Filas</label><select class="form-select" name="por_pagina">@foreach([15,25,50,100] as $cantidad)<option value="{{ $cantidad }}" @selected(($filtros['por_pagina'] ?? 25)==$cantidad)>{{ $cantidad }}</option>@endforeach</select></div>
        <div class="col-12 d-flex gap-2 justify-content-end"><a href="{{ route('reportes.show', $tipo) }}" class="btn btn-outline-secondary">Limpiar</a><x-button type="submit" icon="bi-funnel">Aplicar filtros</x-button></div>
    </form>
</x-card>

<x-card>
    <div class="d-flex justify-content-between align-items-center mb-3"><h2 class="h6 mb-0">Resultados</h2><span class="badge text-bg-light">{{ number_format($total) }} registros</span></div>
    <div class="table-responsive"><table class="table table-striped align-middle report-table"><thead><tr>@foreach($definition['columns'] as $label)<th>{{ $label }}</th>@endforeach</tr></thead><tbody>
        @forelse($resultados as $row)<tr>@foreach($definition['columns'] as $key => $label)<td>{{ $formatValue($row->{$key} ?? null, $definition['formats'][$key] ?? null) }}</td>@endforeach</tr>
        @empty<tr><td colspan="{{ count($definition['columns']) }}" class="text-center text-muted py-5"><i class="bi bi-inbox fs-2 d-block mb-2"></i>No se encontraron resultados con los filtros seleccionados.</td></tr>@endforelse
    </tbody></table></div>
    @if($resultados instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator && $resultados->hasPages())<div class="mt-3">{{ $resultados->links() }}</div>@endif
</x-card>
@if($imprimir)<script>window.addEventListener('load', () => window.print())</script>@endif
@endsection
