@extends('layouts.app')

@section('title', 'Personal')

@php
    $permissions = session('permissions', []);
    $can = static fn (string $permission): bool => in_array('*', $permissions, true) || in_array($permission, $permissions, true);
    $breadcrumbs = [['label' => 'Personal']];
@endphp

@section('content')
<x-page-header title="Personal" subtitle="Administración de colaboradores, cargos, estados y horarios.">
    @if ($can('personal.create'))
        <x-button :href="route('personal.create')" icon="bi-person-plus">Nuevo empleado</x-button>
    @endif
</x-page-header>

<x-card title="Filtros" class="mb-4">
    <form method="GET" action="{{ route('personal.index') }}" class="row g-3">
        <div class="col-lg-4"><label class="form-label" for="texto">Buscar</label><input class="form-control" id="texto" name="texto" value="{{ $filtros['texto'] ?? '' }}" placeholder="Código, nombre, identidad o correo"></div>
        <div class="col-md-4 col-lg-2"><label class="form-label" for="estado_id">Estado</label><select class="form-select" id="estado_id" name="estado_id"><option value="">Todos</option>@foreach($estados as $estado)<option value="{{ $estado->id }}" @selected(($filtros['estado_id'] ?? null) == $estado->id)>{{ $estado->nombre }}</option>@endforeach</select></div>
        <div class="col-md-4 col-lg-2"><label class="form-label" for="cargo_id">Cargo</label><select class="form-select" id="cargo_id" name="cargo_id"><option value="">Todos</option>@foreach($cargos as $cargo)<option value="{{ $cargo->id }}" @selected(($filtros['cargo_id'] ?? null) == $cargo->id)>{{ $cargo->nombre }}</option>@endforeach</select></div>
        <div class="col-md-4 col-lg-2"><label class="form-label" for="fecha_desde">Contratado desde</label><input type="date" class="form-control" id="fecha_desde" name="fecha_desde" value="{{ $filtros['fecha_desde'] ?? '' }}"></div>
        <div class="col-md-4 col-lg-2"><label class="form-label" for="fecha_hasta">Contratado hasta</label><input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta" value="{{ $filtros['fecha_hasta'] ?? '' }}"></div>
        <div class="col-md-3 col-lg-2"><label class="form-label" for="por_pagina">Por página</label><select class="form-select" id="por_pagina" name="por_pagina">@foreach([10,15,25,50,100] as $cantidad)<option value="{{ $cantidad }}" @selected(($filtros['por_pagina'] ?? 15) == $cantidad)>{{ $cantidad }}</option>@endforeach</select></div>
        <div class="col d-flex align-items-end gap-2"><x-button type="submit" icon="bi-search">Filtrar</x-button><x-button :href="route('personal.index')" variant="outline-secondary">Limpiar</x-button></div>
    </form>
</x-card>

<x-card>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th>Empleado</th><th>Cargo</th><th>Estado</th><th>Contacto</th><th>Contratación</th><th class="text-end">Acciones</th></tr></thead>
            <tbody>
            @forelse($personal as $empleado)
                <tr>
                    <td><strong>{{ $empleado->nombre_completo }}</strong><div class="text-muted small">{{ $empleado->codigo_empleado }}@if($empleado->numero_identificacion) · {{ $empleado->numero_identificacion }}@endif</div></td>
                    <td>{{ $empleado->cargo }}</td>
                    <td><span class="badge text-bg-{{ in_array(strtolower($empleado->estado_codigo), ['activo','active']) ? 'success' : 'secondary' }}">{{ $empleado->estado }}</span></td>
                    <td><div>{{ $empleado->telefono ?: '—' }}</div><small class="text-muted">{{ $empleado->correo_electronico ?: 'Sin correo' }}</small></td>
                    <td>{{ \Illuminate\Support\Carbon::parse($empleado->fecha_contratacion)->format('d/m/Y') }}</td>
                    <td class="text-end text-nowrap">
                        @if($can('personal.view'))<a href="{{ route('personal.show', $empleado->id) }}" class="btn btn-sm btn-outline-primary" title="Ver"><i class="bi bi-eye"></i></a>@endif
                        @if($can('personal.update'))<a href="{{ route('personal.edit', $empleado->id) }}" class="btn btn-sm btn-outline-secondary" title="Editar"><i class="bi bi-pencil"></i></a>@endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-5"><i class="bi bi-people fs-2 d-block mb-2"></i>No hay empleados que coincidan con los filtros.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($personal->hasPages())<div class="mt-4">{{ $personal->links() }}</div>@endif
</x-card>
@endsection
