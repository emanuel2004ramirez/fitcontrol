@extends('layouts.app')
@section('title',$pago->numero_recibo)
@section('content')
<x-page-header :title="'Recibo '.$pago->numero_recibo" :subtitle="$pago->cliente.' · '.$pago->metodo"/>
<div class="row g-4">
    <div class="col-lg-8"><x-card title="Pago completado"><div class="row g-3"><div class="col-md-4"><small class="d-block text-muted">Total pagado</small><strong class="fs-4 text-success">{{ number_format($pago->monto,2) }} {{ $pago->moneda }}</strong></div><div class="col-md-4"><small class="d-block text-muted">Fecha</small><strong>{{ \Illuminate\Support\Carbon::parse($pago->pagado_at)->format('d/m/Y H:i') }}</strong></div><div class="col-md-4"><small class="d-block text-muted">Estado</small><strong>{{ $pago->estado }}</strong></div></div>@if($pago->referencia)<hr><small class="text-muted">Referencia</small><div>{{ $pago->referencia }}</div>@endif @if($pago->observaciones)<hr><small class="text-muted">Observación</small><div>{{ $pago->observaciones }}</div>@endif</x-card></div>
    <div class="col-lg-4"><x-card title="Historial">@forelse($historial as $h)<div class="border-bottom py-2"><strong>{{ $h->estado_nuevo }}</strong><div class="small text-muted">{{ $h->cambiado_at }}</div></div>@empty<p>Sin cambios.</p>@endforelse</x-card></div>
</div>
@endsection
