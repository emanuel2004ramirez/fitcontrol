@extends('layouts.app')
@section('title', 'Catálogo de Tipos de Medida')
@php($breadcrumbs = [['label' => 'Catálogos'], ['label' => 'Tipos de Medida']])
@section('content')

<x-page-header title="Tipos de Medida" subtitle="Catálogo de tipos de medida del sistema.">
    <x-button data-bs-toggle="modal" data-bs-target="#modalCrear" icon="bi-plus-lg">Nuevo tipo de medida</x-button>
</x-page-header>

<x-card title="Filtros" class="mb-4">
    <form method="GET" action="{{ route('catalogos.tipos-medida.index') }}" class="row g-3">
        <div class="col-lg-4">
            <label class="form-label">Buscar</label>
            <input class="form-control" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por código, nombre o unidad...">
        </div>
        <div class="col d-flex align-items-end gap-2">
            <x-button type="submit" icon="bi-search">Filtrar</x-button>
            <x-button :href="route('catalogos.tipos-medida.index')" variant="outline-secondary">Limpiar</x-button>
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
                    <th>Unidad</th>
                    <th>Mínimo</th>
                    <th>Máximo</th>
                    <th>Decimales</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tipos as $tipo)
                    <tr>
                        <td><strong>{{ $tipo->codigo }}</strong></td>
                        <td>{{ $tipo->nombre }}</td>
                        <td>{{ $tipo->unidad }}</td>
                        <td>{{ $tipo->valor_minimo ?? '-' }}</td>
                        <td>{{ $tipo->valor_maximo ?? '-' }}</td>
                        <td>{{ $tipo->decimales }}</td>
                        <td>
                            @if($tipo->activo)
                                <span class="badge text-bg-success">Activo</span>
                            @else
                                <span class="badge text-bg-secondary">Inactivo</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditar{{ $tipo->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('catalogos.tipos-medida.destroy', $tipo->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('¿Estás seguro de eliminar este registro?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- Modal Editar -->
                    <div class="modal fade" id="modalEditar{{ $tipo->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('catalogos.tipos-medida.update', $tipo->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title">Editar Tipo de Medida</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                    </div>
                                    <div class="modal-body text-start">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Código <span class="text-danger">*</span></label>
                                                <input type="text" name="codigo" class="form-control" value="{{ $tipo->codigo }}" required maxlength="30">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Unidad <span class="text-danger">*</span></label>
                                                <input type="text" name="unidad" class="form-control" value="{{ $tipo->unidad }}" required maxlength="20">
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                            <input type="text" name="nombre" class="form-control" value="{{ $tipo->nombre }}" required maxlength="50">
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Mínimo</label>
                                                <input type="number" step="any" name="valor_minimo" class="form-control" value="{{ $tipo->valor_minimo }}">
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Máximo</label>
                                                <input type="number" step="any" name="valor_maximo" class="form-control" value="{{ $tipo->valor_maximo }}">
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Decimales <span class="text-danger">*</span></label>
                                                <input type="number" name="decimales" class="form-control" value="{{ $tipo->decimales }}" required min="0" max="4">
                                            </div>
                                        </div>
                                        <div class="form-check form-switch text-start">
                                            <input class="form-check-input" type="checkbox" name="activo" id="activo{{ $tipo->id }}" value="1" @checked($tipo->activo)>
                                            <label class="form-check-label" for="activo{{ $tipo->id }}">Activo</label>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
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
            <form action="{{ route('catalogos.tipos-medida.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Nuevo tipo de medida</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body text-start">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Código <span class="text-danger">*</span></label>
                            <input type="text" name="codigo" class="form-control" required maxlength="30">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Unidad <span class="text-danger">*</span></label>
                            <input type="text" name="unidad" class="form-control" required maxlength="20">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control" required maxlength="50">
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Mínimo</label>
                            <input type="number" step="any" name="valor_minimo" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Máximo</label>
                            <input type="number" step="any" name="valor_maximo" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Decimales <span class="text-danger">*</span></label>
                            <input type="number" name="decimales" class="form-control" required min="0" max="4" value="0">
                        </div>
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
