@extends('layouts.app')
@section('title', 'Detalle de auditoría')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4"><div><h1 class="h3 mb-1">Evento #{{ $auditoria->id }}</h1><p class="text-muted mb-0">{{ $auditoria->evento }} · {{ $auditoria->entidad }}</p></div><a href="{{ route('auditoria.index') }}" class="btn btn-outline-secondary">Volver</a></div>
<div class="row g-4"><div class="col-lg-5"><div class="card border-0 shadow-sm h-100"><div class="card-body"><dl class="row mb-0">
    <dt class="col-5">Fecha</dt><dd class="col-7">{{ \Illuminate\Support\Carbon::parse($auditoria->ocurrido_at)->format('d/m/Y H:i:s') }}</dd><dt class="col-5">Usuario</dt><dd class="col-7">{{ $auditoria->usuario }}</dd><dt class="col-5">Entidad / ID</dt><dd class="col-7">{{ $auditoria->entidad }} / {{ $auditoria->entidad_id ?? '—' }}</dd><dt class="col-5">IP</dt><dd class="col-7">{{ $auditoria->ip ?? '—' }}</dd><dt class="col-5">Motivo</dt><dd class="col-7">{{ $auditoria->motivo ?? '—' }}</dd><dt class="col-5">Navegador</dt><dd class="col-7 text-break small">{{ $auditoria->user_agent ?? '—' }}</dd>
</dl></div></div></div><div class="col-lg-7"><div class="card border-0 shadow-sm"><div class="card-header bg-white fw-semibold">Datos registrados</div><div class="card-body"><pre class="bg-light border rounded p-3 mb-0 small" style="white-space:pre-wrap">{{ json_encode(json_decode($auditoria->valores_nuevos ?? 'null', true), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre></div></div></div></div>
@endsection
