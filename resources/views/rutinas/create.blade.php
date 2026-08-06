@extends('layouts.app')
@section('title','Nueva rutina')
@section('content')
<x-page-header title="Nueva rutina" subtitle="Solo aparecen clientes con membresía vigente y totalmente pagada."/>
<x-card><form method="POST" action="{{ route('rutinas.store') }}" class="row g-3">@csrf
<div class="col-md-6"><label class="form-label">Cliente</label><select class="form-select" name="cliente_id" required><option value="">Seleccione</option>@foreach($clientes as $c)<option value="{{ $c->id }}">{{ $c->numero_socio }} · {{ $c->nombre }}</option>@endforeach</select></div>
@if($entrenadorActualId)<input type="hidden" name="entrenador_id" value="{{ $entrenadorActualId }}"><div class="col-md-6"><label class="form-label">Entrenador responsable</label><input class="form-control" value="Usuario actual" disabled></div>@else<div class="col-md-6"><label class="form-label">Entrenador responsable</label><select class="form-select" name="entrenador_id" required><option value="">Seleccione</option>@foreach($entrenadores as $e)<option value="{{ $e->id }}">{{ $e->nombre }}</option>@endforeach</select></div>@endif
<div class="col-md-8"><label class="form-label">Nombre</label><input class="form-control" name="nombre" placeholder="Ej. Acondicionamiento inicial" required></div><div class="col-md-4"><label class="form-label">Inicio</label><input type="date" class="form-control" name="fecha_inicio" value="{{ now()->toDateString() }}" required></div>
<div class="col-md-6"><label class="form-label">Estado</label><select class="form-select" name="estado_rutina_id" required>@foreach($estados as $e)<option value="{{ $e->id }}">{{ $e->nombre }}</option>@endforeach</select></div><div class="col-md-6"><label class="form-label">Descripción <span class="text-muted">(opcional)</span></label><input class="form-control" name="descripcion"></div>
<div class="text-end"><x-button type="submit">Crear y agregar ejercicios</x-button></div></form></x-card>
@endsection
