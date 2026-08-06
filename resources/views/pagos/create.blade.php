@extends('layouts.app')
@section('title','Registrar pago')
@section('content')
<x-page-header title="Registrar pago" subtitle="El pago se registra completo y queda aplicado automáticamente."/>
<x-card>
<form method="POST" action="{{ route('pagos.store') }}" class="row g-3">@csrf
    <input type="hidden" name="idempotency_key" value="{{ (string) \Illuminate\Support\Str::uuid() }}">
    <div class="col-md-8"><label class="form-label">Cliente y membresía</label><select class="form-select" id="membresia" name="membresia_id" required><option value="">Seleccione</option>@foreach($membresias as $m)<option value="{{ $m->id }}" data-total="{{ $m->total }}" data-moneda="{{ $m->moneda }}">{{ $m->numero_socio }} · {{ $m->cliente }} · {{ $m->tipo }} — {{ number_format($m->total,2) }} {{ $m->moneda }}</option>@endforeach</select></div>
    <div class="col-md-4"><label class="form-label">Total a pagar</label><input class="form-control fw-bold" id="total" value="Seleccione una membresía" disabled></div>
    <div class="col-md-6"><label class="form-label">Método de pago</label><select class="form-select" name="metodo_pago_id" required>@foreach($metodos as $m)@if($m->activo)<option value="{{ $m->id }}">{{ $m->nombre }}</option>@endif @endforeach</select></div>
    <div class="col-md-6"><label class="form-label">Referencia <span class="text-muted">(opcional para efectivo)</span></label><input class="form-control" name="referencia" placeholder="Transferencia, depósito o autorización"></div>
    <div class="col-12"><label class="form-label">Observación <span class="text-muted">(opcional)</span></label><textarea class="form-control" name="observaciones" rows="2"></textarea></div>
    <div class="text-end"><x-button type="submit">Pagar total</x-button></div>
</form>
</x-card>
@push('scripts')<script>document.getElementById('membresia').addEventListener('change',e=>{const o=e.target.selectedOptions[0];document.getElementById('total').value=o?.dataset.total?Number(o.dataset.total).toLocaleString('es-HN',{minimumFractionDigits:2})+' '+o.dataset.moneda:'Seleccione una membresía';});</script>@endpush
@endsection
