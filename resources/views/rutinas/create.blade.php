@extends('layouts.app')
@section('title','Nueva rutina')
@section('content')
<x-page-header title="Nueva rutina" subtitle="Rutina personalizada para el cliente seleccionado en el plan semanal." />
<x-card><form method="POST" action="{{ route('rutinas.store') }}" class="row g-3">@csrf
<input type="hidden" name="cliente_id" value="{{ $clienteSeleccionado->id }}">
@if($diaSemana)<input type="hidden" name="dia_semana" value="{{ $diaSemana }}">@endif
<div class="col-md-6"><label class="form-label">Cliente</label><input class="form-control" value="{{ $clienteSeleccionado->numero_socio }} · {{ $clienteSeleccionado->nombre }}" disabled></div>
@if($esEntrenador)
<div class="col-md-6"><label class="form-label">Entrenador responsable</label><input class="form-control" value="Usuario actual" disabled><div class="form-text">Se asigna automáticamente desde tu cuenta.</div></div>
@elseif($entrenadorAsignado)
<div class="col-md-6"><label class="form-label">Entrenador responsable</label><input class="form-control" value="{{ $entrenadorAsignado->nombre }}" disabled><div class="form-text">Este cliente ya tiene rutinas asignadas a este entrenador.</div></div>
@else
<div class="col-md-6"><label class="form-label">Entrenador responsable</label><select class="form-select" name="entrenador_id" required><option value="">Seleccione</option>@foreach($entrenadores as $e)<option value="{{ $e->id }}" @selected(old('entrenador_id')==$e->id)>{{ $e->nombre }}</option>@endforeach</select></div>
@endif
<div class="col-md-8"><label class="form-label">Nombre</label><input class="form-control" name="nombre" placeholder="Ej. Acondicionamiento inicial" required></div>
<div class="col-md-4"><label class="form-label">Inicio</label><input type="date" class="form-control" name="fecha_inicio" value="{{ now()->toDateString() }}" required></div>
<div class="col-md-6"><label class="form-label">Estado</label><select class="form-select" name="estado_rutina_id" required>@foreach($estados as $e)<option value="{{ $e->id }}">{{ $e->nombre }}</option>@endforeach</select></div>
<div class="col-md-6"><label class="form-label">Descripción <span class="text-muted">(opcional)</span></label><input class="form-control" name="descripcion" data-optional="true"></div>
<div class="text-end"><x-button type="submit">Crear y agregar ejercicios</x-button></div>
</form></x-card>
@endsection
