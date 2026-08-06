@extends('layouts.app')
@section('title', 'Catálogo de Métodos de Pago')
@php($breadcrumbs = [['label' => 'Catálogos'], ['label' => 'Métodos de Pago']])
@section('content')

<x-page-header title="Métodos de Pago" subtitle="Catálogo de métodos de pago del sistema.">
    <x-button data-bs-toggle="modal" data-bs-target="#modalCrear" icon="bi-plus-lg">Nuevo Método</x-button>
</x-page-header>

<x-card title="Filtros" class="mb-4">
    <form method="GET" action="{{ route('catalogos.metodos-pago.index') }}" class="row g-3">
        <div class="col-lg-4">
            <label class="form-label">Buscar</label>
            <input class="form-control" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por código o nombre...">
        </div>
        <div class="col d-flex align-items-end gap-2">
            <x-button type="submit" icon="bi-search">Filtrar</x-button>
            <x-button :href="route('catalogos.metodos-pago.index')" variant="outline-secondary">Limpiar</x-button>
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
                    <th>Req. Referencia</th>
                    <th>Activo</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($metodos as $metodo)
                    <tr>
                        <td><strong>{{ $metodo->codigo }}</strong></td>
                        <td>{{ $metodo->nombre }}</td>
                        <td>
                            @if($metodo->requiere_referencia)
                                <span class="badge text-bg-primary">Sí</span>
                            @else
                                <span class="badge text-bg-secondary">No</span>
                            @endif
                        </td>
                        <td>
                            @if($metodo->activo)
                                <span class="badge text-bg-success">Activo</span>
                            @else
                                <span class="badge text-bg-secondary">Inactivo</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditar{{ $metodo->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('catalogos.metodos-pago.destroy', $metodo->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('¿Estás seguro de eliminar este registro?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- Modal Editar -->
                    <div class="modal fade" id="modalEditar{{ $metodo->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('catalogos.metodos-pago.update', $metodo->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title">Editar Método de Pago</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                    </div>
                                    <div class="modal-body text-start">
                                        <div class="mb-3">
                                            <label class="form-label">Código <span class="text-danger">*</span></label>
                                            <input type="text" name="codigo" class="form-control" value="{{ $metodo->codigo }}" required maxlength="30">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                            <input type="text" name="nombre" class="form-control" value="{{ $metodo->nombre }}" required maxlength="50">
                                        </div>
                                        <div class="form-check form-switch text-start mb-2">
                                            <input class="form-check-input" type="checkbox" name="requiere_referencia" id="requiere_referencia{{ $metodo->id }}" value="1" @checked($metodo->requiere_referencia)>
                                            <label class="form-check-label" for="requiere_referencia{{ $metodo->id }}">Requiere Referencia</label>
                                        </div>
                                        <div class="form-check form-switch text-start">
                                            <input class="form-check-input" type="checkbox" name="activo" id="activo{{ $metodo->id }}" value="1" @checked($metodo->activo)>
                                            <label class="form-check-label" for="activo{{ $metodo->id }}">Activo</label>
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
                        <td colspan="5" class="text-center text-muted py-5">
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
            <form action="{{ route('catalogos.metodos-pago.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Nuevo Método de Pago</h5>
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
                    <div class="form-check form-switch text-start mb-2">
                        <input class="form-check-input" type="checkbox" name="requiere_referencia" id="requiere_referenciaNuevo" value="1">
                        <label class="form-check-label" for="requiere_referenciaNuevo">Requiere Referencia</label>
                    </div>
                    <div class="form-check form-switch text-start">
                        <input class="form-check-input" type="checkbox" name="activo" id="activoNuevo" value="1" checked>
                        <label class="form-check-label" for="activoNuevo">Activo</label>
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
