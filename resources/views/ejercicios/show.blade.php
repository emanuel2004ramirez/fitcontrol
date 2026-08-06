@extends('layouts.app')
@section('title', $ejercicio->nombre)
@php($permissions = session('permissions', []))
@php($can = static fn(string $p): bool => in_array('*', $permissions, true) || in_array($p, $permissions, true))
@section('content')
<x-page-header :title="$ejercicio->nombre" :subtitle="$ejercicio->codigo.' · '.$ejercicio->estado">@if($can('ejercicios.update'))<x-button :href="route('ejercicios.edit', $ejercicio->id)">Editar</x-button>@endif</x-page-header>
<div class="row g-4"><div class="col-lg-8"><x-card title="Información"><p>{{ $ejercicio->descripcion ?: 'Sin descripción.' }}</p><h6>Instrucciones</h6><p class="text-pre-wrap">{{ $ejercicio->instrucciones ?: 'Sin instrucciones.' }}</p>@if($ejercicio->video_url)<a href="{{ $ejercicio->video_url }}" target="_blank" rel="noopener" class="btn btn-outline-primary mt-3">Ver video</a>@endif</x-card></div><div class="col-lg-4"><x-card title="Clasificación"><dl class="mb-0"><dt>Grupo muscular</dt><dd>{{ $ejercicio->grupo_muscular ?: 'Sin asignar' }}</dd><dt>Equipamiento</dt><dd class="mb-0">{{ $ejercicio->equipamiento ?: '—' }}</dd></dl></x-card></div></div>
@if($can('ejercicios.changeStatus'))<x-card title="Cambiar estado" class="mt-4"><form method="POST" action="{{ route('ejercicios.estado',$ejercicio->id) }}" class="d-flex gap-2">@csrf @method('PATCH')<select class="form-select" name="estado_ejercicio_id">@foreach($estados as $e)<option value="{{ $e->id }}">{{ $e->nombre }}</option>@endforeach</select><x-button type="submit">Cambiar</x-button></form></x-card>@endif
@endsection
