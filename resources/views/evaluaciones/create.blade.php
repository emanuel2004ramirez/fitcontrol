@extends('layouts.app')
@section('title', 'Nueva evaluación')
@section('content')
<x-page-header title="Registrar evaluación física" subtitle="Registra la información general y las medidas corporales." />
<form method="POST" action="{{ route('evaluaciones.store') }}">@csrf
<x-card title="Información general" class="mb-4"><div class="row g-3">
<div class="col-md-4"><label class="form-label">Cliente</label><select class="form-select" name="cliente_id" required><option value="">Seleccione</option>@foreach($clientes as $c)<option value="{{ $c->id }}" @selected(old('cliente_id')==$c->id)>{{ $c->numero_socio }} · {{ $c->nombre }}</option>@endforeach</select></div>
<div class="col-md-4"><label class="form-label">Evaluador</label><select class="form-select" name="evaluador_id" required><option value="">Seleccione</option>@foreach($evaluadores as $e)<option value="{{ $e->id }}" @selected(old('evaluador_id')==$e->id)>{{ $e->nombre }}</option>@endforeach</select></div>
<div class="col-md-4"><label class="form-label">Fecha y hora</label><input type="datetime-local" class="form-control" name="evaluada_at" value="{{ old('evaluada_at',now()->format('Y-m-d\TH:i')) }}" max="{{ now()->format('Y-m-d\TH:i') }}" required></div>
<div class="col-md-6"><label class="form-label">Método de evaluación</label><input class="form-control" name="metodo" value="{{ old('metodo') }}" placeholder="Ej. antropometría o bioimpedancia" required></div>
<div class="col-md-6"><label class="form-label">Observaciones</label><input class="form-control" name="observaciones" value="{{ old('observaciones') }}"></div>
</div></x-card>
<x-card title="Medidas corporales"><div class="row g-3">
@foreach($tiposMedida as $tipo) @if($tipo->activo)
<div class="col-md-6 col-xl-4"><div class="border rounded-3 p-3 h-100"><label class="form-label fw-semibold">{{ $tipo->nombre }} ({{ $tipo->unidad }})</label>
<input type="hidden" name="medidas[{{ $tipo->id }}][tipo_medida_id]" value="{{ $tipo->id }}">
<input type="number" class="form-control" name="medidas[{{ $tipo->id }}][valor]" value="{{ old('medidas.'.$tipo->id.'.valor') }}" step="{{ $tipo->decimales > 0 ? '0.'.str_repeat('0',$tipo->decimales-1).'1' : '1' }}" @if($tipo->valor_minimo!==null) min="{{ $tipo->valor_minimo }}" @endif @if($tipo->valor_maximo!==null) max="{{ $tipo->valor_maximo }}" @endif required>
<input class="form-control form-control-sm mt-2" name="medidas[{{ $tipo->id }}][instrumento]" value="{{ old('medidas.'.$tipo->id.'.instrumento') }}" placeholder="Instrumento" required>
@if($tipo->valor_minimo!==null || $tipo->valor_maximo!==null)<small class="text-muted">Rango: {{ $tipo->valor_minimo ?? '—' }}–{{ $tipo->valor_maximo ?? '—' }} {{ $tipo->unidad }}</small>@endif
</div></div>
@endif @endforeach
</div><div class="d-flex justify-content-end gap-2 mt-4"><a href="{{ route('evaluaciones.index') }}" class="btn btn-outline-secondary">Cancelar</a><x-button type="submit">Guardar evaluación completa</x-button></div></x-card>
</form>
@endsection
