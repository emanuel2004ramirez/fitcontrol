@extends('layouts.app')
@section('title', 'Auditoría')
@section('content')
<div class="mb-4"><h1 class="h3 mb-1">Auditoría del sistema</h1><p class="text-muted mb-0">Bitácora inmutable de operaciones y accesos.</p></div>
<div class="card border-0 shadow-sm mb-4"><div class="card-body"><form method="GET" class="row g-3">
    <div class="col-lg-3"><label class="form-label">Búsqueda</label><input class="form-control" name="texto" value="{{ $filtros['texto'] ?? '' }}" placeholder="Usuario, ID o motivo"></div>
    <div class="col-lg-2"><label class="form-label">Evento</label><input class="form-control" name="evento" value="{{ $filtros['evento'] ?? '' }}" placeholder="CREAR, EDITAR..."></div>
    <div class="col-lg-2"><label class="form-label">Entidad</label><input class="form-control" name="entidad" value="{{ $filtros['entidad'] ?? '' }}"></div>
    <div class="col-lg-2"><label class="form-label">Desde</label><input type="date" class="form-control" name="desde" value="{{ $filtros['desde'] ?? '' }}"></div>
    <div class="col-lg-2"><label class="form-label">Hasta</label><input type="date" class="form-control" name="hasta" value="{{ $filtros['hasta'] ?? '' }}"></div>
    <div class="col-lg-1 d-flex align-items-end"><button class="btn btn-primary w-100" title="Filtrar"><i class="bi bi-search"></i></button></div>
</form></div></div>
<div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
    <thead><tr><th>Fecha</th><th>Usuario</th><th>Evento</th><th>Entidad</th><th>Registro</th><th>IP</th><th></th></tr></thead><tbody>
    @forelse($auditorias as $registro)
    <tr><td class="text-nowrap">{{ \Illuminate\Support\Carbon::parse($registro->ocurrido_at)->format('d/m/Y H:i:s') }}</td><td>{{ $registro->usuario }}</td><td><span class="badge text-bg-secondary">{{ $registro->evento }}</span></td><td>{{ $registro->entidad }}</td><td>{{ $registro->entidad_id ?? '—' }}</td><td>{{ $registro->ip ?? '—' }}</td><td><a class="btn btn-sm btn-outline-primary" href="{{ route('auditoria.show', $registro->id) }}"><i class="bi bi-eye"></i></a></td></tr>
    @empty<tr><td colspan="7" class="text-center text-muted py-5">No se encontraron eventos.</td></tr>@endforelse
    </tbody></table></div><div class="card-footer bg-white">{{ $auditorias->links() }}</div></div>
@endsection
