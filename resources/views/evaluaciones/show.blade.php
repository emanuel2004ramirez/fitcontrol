@extends('layouts.app')
@section('title', 'Evaluación')
@section('content')
<x-page-header :title="$evaluacion->cliente" :subtitle="'Evaluación del '.\Illuminate\Support\Carbon::parse($evaluacion->evaluada_at)->format('d/m/Y H:i')"/>

<div class="row g-4">
    <div class="col-lg-7">
        <x-card title="Medidas">
            <form method="POST" action="{{ route('evaluaciones.medidas.store', $evaluacion->id) }}" class="row g-2 mb-4">@csrf
                <input type="hidden" name="evaluacion_fisica_id" value="{{ $evaluacion->id }}">
                <div class="col"><select class="form-select" name="tipo_medida_id">@foreach($tiposMedida as $tipo) @if($tipo->activo)<option value="{{ $tipo->id }}">{{ $tipo->nombre }} ({{ $tipo->unidad }})</option>@endif @endforeach</select></div>
                <div class="col-3"><input type="number" step=".0001" class="form-control" name="valor" placeholder="Valor" required></div>
                <div class="col-3"><input class="form-control" name="instrumento" placeholder="Instrumento" required></div>
                <div class="col-auto"><x-button type="submit">Guardar</x-button></div>
            </form>
            <div class="table-responsive"><table class="table align-middle"><thead><tr><th>Medida</th><th>Valor</th><th>Instrumento</th></tr></thead><tbody>
                @foreach($medidas as $medida)<tr><td>{{ $medida->nombre }}</td><td><strong>{{ number_format($medida->valor, $medida->decimales) }} {{ $medida->unidad_snapshot }}</strong></td><td>{{ $medida->instrumento ?: '—' }}</td></tr>@endforeach
            </tbody></table></div>
        </x-card>
    </div>

    <div class="col-lg-5">
        <x-card title="Comparar progreso">
            <form method="GET" action="{{ route('evaluaciones.comparar', $evaluacion->id) }}" class="d-flex gap-2 mb-3">
                <select class="form-select" name="evaluacion_comparada_id" required><option value="">Selecciona otra evaluación</option>@foreach($historial as $item) @if($item->id != $evaluacion->id)<option value="{{ $item->id }}" @selected(request('evaluacion_comparada_id') == $item->id)>{{ \Illuminate\Support\Carbon::parse($item->evaluada_at)->format('d/m/Y H:i') }}</option>@endif @endforeach</select>
                <x-button type="submit">Comparar</x-button>
            </form>

            @if($comparacion)
                @php($primera = $comparacion[0])
                <div class="table-responsive"><table class="table table-sm align-middle">
                    <thead><tr><th>Medida</th><th class="text-end">Anterior<small class="d-block fw-normal text-muted">{{ \Illuminate\Support\Carbon::parse($primera->fecha_anterior)->format('d/m/Y') }}</small></th><th class="text-end">Actual<small class="d-block fw-normal text-muted">{{ \Illuminate\Support\Carbon::parse($primera->fecha_actual)->format('d/m/Y') }}</small></th><th class="text-end">Cambio</th></tr></thead>
                    <tbody>@foreach($comparacion as $item)
                        @php($sube = $item->diferencia > 0) @php($baja = $item->diferencia < 0)
                        <tr>
                            <td>{{ $item->nombre }}</td>
                            <td class="text-end text-nowrap">{{ number_format($item->valor_anterior, $item->decimales) }} {{ $item->unidad }}</td>
                            <td class="text-end text-nowrap"><strong>{{ number_format($item->valor_actual, $item->decimales) }} {{ $item->unidad }}</strong></td>
                            <td class="text-end text-nowrap {{ $sube ? 'text-info' : ($baja ? 'text-warning' : 'text-muted') }}">
                                <i class="bi {{ $sube ? 'bi-arrow-up' : ($baja ? 'bi-arrow-down' : 'bi-dash') }}"></i>
                                {{ $item->diferencia > 0 ? '+' : '' }}{{ number_format($item->diferencia, $item->decimales) }} {{ $item->unidad }}
                                @if($item->variacion_porcentual !== null)<small class="d-block">{{ $item->variacion_porcentual > 0 ? '+' : '' }}{{ number_format($item->variacion_porcentual, 2) }}%</small>@endif
                                @if(abs($item->variacion_porcentual ?? 0) >= 50)<span class="badge text-bg-danger">Verificar dato</span>@endif
                            </td>
                        </tr>
                    @endforeach</tbody>
                </table></div>
            @elseif(request()->has('evaluacion_comparada_id'))
                <div class="alert alert-warning mb-0">Las evaluaciones no tienen medidas en común para comparar.</div>
            @else
                <p class="text-muted mb-0">Selecciona otra evaluación del cliente para conocer su cambio real.</p>
            @endif
        </x-card>

        <x-card title="Historial del cliente" class="mt-4"><ul class="list-group list-group-flush">@foreach($historial as $item)<li class="list-group-item px-0"><a href="{{ route('evaluaciones.show', $item->id) }}">{{ \Illuminate\Support\Carbon::parse($item->evaluada_at)->format('d/m/Y H:i') }}</a><div class="small text-muted">{{ $item->evaluador }} · {{ $item->medidas }} medidas</div></li>@endforeach</ul></x-card>
    </div>
</div>
@endsection
