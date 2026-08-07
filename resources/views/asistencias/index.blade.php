@extends('layouts.app')
@section('title', 'Asistencias')
@php($breadcrumbs = [['label' => 'Asistencias']])
@section('content')

<x-page-header title="Asistencias" subtitle="Control de accesos y clientes en el establecimiento.">
    @can('asistencias.create')
        <x-button data-bs-toggle="modal" data-bs-target="#modalCrear" icon="bi-plus-lg">Registrar Entrada</x-button>
    @endcan
</x-page-header>

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<x-card title="Filtros" class="mb-4">
    <form method="GET" action="{{ route('asistencias.index') }}" class="row g-3">
        <div class="col-lg-4">
            <label class="form-label">Buscar Cliente</label>
            <input class="form-control" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por nombre o número de socio...">
        </div>
        <div class="col d-flex align-items-end gap-2">
            <x-button type="submit" icon="bi-search">Filtrar</x-button>
            <x-button :href="route('asistencias.index')" variant="outline-secondary">Limpiar</x-button>
        </div>
    </form>
</x-card>

<div class="row g-4 mb-4">
    @forelse($asistencias as $asistencia)
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle me-3">
                            <i class="bi bi-person-fill fs-4"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-1 fw-bold">{{ $asistencia->cliente ?? 'Cliente' }}</h5>
                            <p class="card-text text-muted mb-0 small">Socio: {{ $asistencia->numero_socio ?? '-' }}</p>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted small">Membresía</span>
                            <span class="fw-semibold small">{{ $asistencia->tipo_membresia ?? '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted small">Hora Entrada</span>
                            <span class="fw-semibold small">{{ \Carbon\Carbon::parse($asistencia->entrada_at)->format('h:i A') }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted small">Tiempo transcurrido</span>
                            <span class="fw-semibold small text-primary">
                                {{ round($asistencia->minutos) }} min
                            </span>
                        </div>
                    </div>
                    
                    @can('asistencias.update')
                        <div class="mt-auto pt-3 border-top">
                            <form action="{{ route('asistencias.update', $asistencia->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="bi bi-box-arrow-right me-1"></i> Marcar Salida
                                </button>
                            </form>
                        </div>
                    @endcan
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="text-center py-5 bg-white rounded shadow-sm">
                <div class="text-muted mb-3">
                    <i class="bi bi-people fs-1"></i>
                </div>
                <h5 class="text-muted">No hay clientes actualmente en las instalaciones.</h5>
            </div>
        </div>
    @endforelse
</div>

<div class="d-flex justify-content-center">
    {{ $asistencias->links() }}
</div>

@can('asistencias.create')
<!-- Modal Crear (Check-in) -->
<div class="modal fade" id="modalCrear" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('asistencias.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Entrada</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body text-start">
                    
                    <div class="mb-3">
                        <label class="form-label">Cliente (Con Membresía Activa) <span class="text-danger">*</span></label>
                        <select name="cliente_membresia" class="form-select" required>
                            <option value="">Seleccione un cliente...</option>
                            @foreach($clientesElegibles as $ce)
                                <option value="{{ $ce->id }}-{{ $ce->membresia_id }}">
                                    {{ $ce->numero_socio }} - {{ $ce->nombre }} ({{ $ce->membresia }})
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">Solo aparecen clientes al día y sin bloqueo.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Observaciones</label>
                        <textarea name="observaciones" class="form-control" rows="2"></textarea>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Registrar Entrada</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan
@endsection
