@extends('layouts.app')

@section('title', 'Asistencias')

@php
    $permissions = session('permissions', []);
    $can = static fn (string $permission): bool => in_array('*', $permissions, true) || in_array($permission, $permissions, true);
@endphp

@section('content')
    <x-page-header title="Asistencias" subtitle="Control de entradas, salidas y aforo.">
        @if ($can('asistencias.create'))
            <x-button :href="route('asistencias.create')">Registrar entrada</x-button>
        @endif
    </x-page-header>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <x-stat-card label="Dentro ahora" :value="$resumen->dentro_ahora ?? 0" icon="bi-people" />
        </div>
        <div class="col-md-4">
            <x-stat-card label="Entradas hoy" :value="$resumen->entradas_hoy ?? 0" icon="bi-box-arrow-in-right" tone="success" />
        </div>
        <div class="col-md-4">
            <x-stat-card label="Promedio hoy (min)" :value="$resumen->promedio_minutos_hoy ?? 0" icon="bi-clock" />
        </div>
    </div>

    <x-card title="Historial">
        <form method="GET" class="d-flex gap-2 mb-3">
            <input class="form-control" name="texto" value="{{ $filtros['texto'] ?? '' }}" placeholder="Socio o cliente">
            <input type="hidden" name="solo_abiertas" value="0">
            <label class="form-check">
                <input class="form-check-input" type="checkbox" name="solo_abiertas" value="1" @checked($filtros['solo_abiertas'] ?? false)>
                Abiertas
            </label>
            <x-button type="submit">Buscar</x-button>
        </form>

        <table class="table">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Entrada</th>
                    <th>Salida</th>
                    <th>Duracion</th>
                    <th>Metodo</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($asistencias as $a)
                    <tr>
                        <td>{{ $a->cliente }}</td>
                        <td>{{ \Illuminate\Support\Carbon::parse($a->entrada_at)->format('d/m/Y H:i') }}</td>
                        <td>{{ $a->salida_at ? \Illuminate\Support\Carbon::parse($a->salida_at)->format('d/m/Y H:i') : 'Dentro' }}</td>
                        <td>{{ $a->minutos }} min</td>
                        <td>{{ $a->metodo_registro }}</td>
                        <td>
                            @if ($a->abierta && $can('asistencias.update'))
                                <form method="POST" action="{{ route('asistencias.salida', $a->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn btn-sm btn-success">Registrar salida</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">Sin asistencias.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{ $asistencias->links() }}
    </x-card>
@endsection
