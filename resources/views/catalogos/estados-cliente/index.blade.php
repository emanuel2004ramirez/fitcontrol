@extends('layouts.app')
@section('title', 'Catálogo de Estados de Cliente')
@php($breadcrumbs = [['label' => 'Catálogos'], ['label' => 'Estados de Cliente']])
@section('content')

<x-page-header title="Estados de Cliente" subtitle="Catálogo de estados de cliente del sistema.">
    <x-button data-bs-toggle="modal" data-bs-target="#modalCrear" icon="bi-plus-lg">Nuevo Estado</x-button>
</x-page-header>

<x-card title="Filtros" class="mb-4">
    <form method="GET" action="{{ route('catalogos.estados-cliente.index') }}" class="row g-3">
        <div class="col-lg-4">
            <label class="form-label">Buscar</label>
            <input class="form-control" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por código o nombre...">
        </div>
        <div class="col d-flex align-items-end gap-2">
            <x-button type="submit" icon="bi-search">Filtrar</x-button>
            <x-button :href="route('catalogos.estados-cliente.index')" variant="outline-secondary">Limpiar</x-button>
        </div>
    </form>
</x-card>

<x-card>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Orden</th>
                    <th>Estado</th>
                    <th>Terminal</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($estados as $estado)
                    <tr>
                        <td><strong>{{ $estado->codigo }}</strong></td>
                        <td>{{ $estado->nombre }}</td>
                        <td>{{ $estado->orden }}</td>
                        <td>
                            @if($estado->activo)
                                <span class="badge text-bg-success">Activo</span>
                            @else
                                <span class="badge text-bg-danger">Inactivo</span>
                            @endif
                        </td>
                        <td>
                            @if($estado->es_terminal)
                                <span class="badge text-bg-primary">Sí</span>
                            @else
                                <span class="badge text-bg-secondary">No</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditar{{ $estado->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('catalogos.estados-cliente.destroy', $estado->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('¿Estás seguro de eliminar este registro?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- Modal Editar -->
                    <div class="modal fade" id="modalEditar{{ $estado->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('catalogos.estados-cliente.update', $estado->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title">Editar Estado de Cliente</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                    </div>
                                    <div class="modal-body text-start">
                                        <div class="mb-3">
                                            <label class="form-label">Código <span class="text-danger">*</span></label>
                                            <input type="text" name="codigo" class="form-control" value="{{ $estado->codigo }}" required maxlength="30">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                            <input type="text" name="nombre" class="form-control" value="{{ $estado->nombre }}" required maxlength="50">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Orden <span class="text-danger">*</span></label>
                                            <input type="number" name="orden" class="form-control" value="{{ $estado->orden }}" required min="0">
                                        </div>
                                        <div class="form-check form-switch text-start mb-2">
                                            <input class="form-check-input" type="checkbox" name="activo" id="activo{{ $estado->id }}" value="1" @checked($estado->activo)>
                                            <label class="form-check-label" for="activo{{ $estado->id }}">Activo</label>
                                        </div>
                                        <div class="form-check form-switch text-start">
                                            <input class="form-check-input" type="checkbox" name="es_terminal" id="es_terminal{{ $estado->id }}" value="1" @checked($estado->es_terminal)>
                                            <label class="form-check-label" for="es_terminal{{ $estado->id }}">Es Terminal</label>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            No se han encontrado registros.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-card>

<!-- Modal Crear -->
<div class="modal fade" id="modalCrear" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('catalogos.estados-cliente.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Nuevo Estado de Cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body text-start">
                    <div class="mb-3">
                        <label class="form-label">Código <span class="text-danger">*</span></label>
                        <input type="text" name="codigo" class="form-control" required maxlength="30">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control" required maxlength="50">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Orden <span class="text-danger">*</span></label>
                        <input type="number" name="orden" class="form-control" required min="0" value="0">
                    </div>
                    <div class="form-check form-switch text-start mb-2">
                        <input class="form-check-input" type="checkbox" name="activo" id="activoNuevo" value="1" checked>
                        <label class="form-check-label" for="activoNuevo">Activo</label>
                    </div>
                    <div class="form-check form-switch text-start">
                        <input class="form-check-input" type="checkbox" name="es_terminal" id="es_terminalNuevo" value="1">
                        <label class="form-check-label" for="es_terminalNuevo">Es Terminal</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
