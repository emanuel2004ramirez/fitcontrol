@extends('layouts.app')
@section('title','Nueva membresía')
@php($breadcrumbs=[['label'=>'Membresías','url'=>route('membresias.index')],['label'=>'Nueva']])
@section('content')
<x-page-header title="Nueva membresía" subtitle="La fecha final se calcula automáticamente según la duración del plan."/>
<x-card>
<form method="POST" action="{{ route('membresias.store') }}" class="row g-3">@csrf
    <div class="col-md-6"><label class="form-label">Cliente</label><select class="form-select" name="cliente_id" required><option value="">Seleccione</option>@foreach($clientes as $c)<option value="{{ $c->id }}">{{ $c->numero_socio }} · {{ $c->nombre }}</option>@endforeach</select></div>
    <div class="col-md-6"><label class="form-label">Plan y precio vigente</label><select class="form-select" name="precio_membresia_id" id="precio" required><option value="">Seleccione</option>@foreach($precios as $p)<option value="{{ $p->id }}" data-tipo="{{ $p->tipo_membresia_id }}" data-dias="{{ $p->duracion_dias }}">{{ $p->tipo }} · {{ number_format($p->precio,2) }} {{ $p->moneda }}</option>@endforeach</select><input type="hidden" name="tipo_membresia_id" id="tipo"></div>
    <div class="col-md-6"><label class="form-label">Estado inicial</label><select class="form-select" name="estado_membresia_id" required>@foreach($estados as $e)@if(!$e->es_terminal)<option value="{{ $e->id }}">{{ $e->nombre }}</option>@endif @endforeach</select></div>
    <div class="col-md-6"><label class="form-label">Fecha de inicio</label><input type="date" class="form-control" id="inicio" name="fecha_inicio" value="{{ now()->toDateString() }}" required><div id="fin-ayuda" class="form-text">Seleccione un plan para ver la fecha final.</div></div>
    <input type="hidden" name="origen" value="NUEVA">
    <div class="text-end"><x-button :href="route('membresias.index')" variant="outline-secondary" class="me-2">Cancelar</x-button><x-button type="submit">Crear membresía</x-button></div>
</form>
</x-card>
@push('scripts')<script>
const precio=document.getElementById('precio'),inicio=document.getElementById('inicio'),ayuda=document.getElementById('fin-ayuda');
function calcular(){const op=precio.selectedOptions[0];document.getElementById('tipo').value=op?.dataset.tipo||'';const dias=Number(op?.dataset.dias||0);if(!inicio.value||!dias){ayuda.textContent='Seleccione un plan para ver la fecha final.';return;}const fin=new Date(inicio.value+'T12:00:00');fin.setDate(fin.getDate()+dias-1);ayuda.textContent='Finaliza automáticamente el '+fin.toLocaleDateString('es-HN');}
precio.addEventListener('change',calcular);inicio.addEventListener('change',calcular);calcular();
</script>@endpush
@endsection
