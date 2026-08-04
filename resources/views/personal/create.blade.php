@extends('layouts.app')
@section('title', 'Nuevo empleado')
@php($breadcrumbs = [['label' => 'Personal', 'url' => route('personal.index')], ['label' => 'Nuevo empleado']])
@section('content')
<x-page-header title="Nuevo empleado" subtitle="Registra los datos laborales y personales básicos." />
<x-card>
    <form method="POST" action="{{ route('personal.store') }}">@csrf @include('personal._form')<div class="d-flex justify-content-end gap-2 mt-4"><x-button :href="route('personal.index')" variant="outline-secondary">Cancelar</x-button><x-button type="submit" icon="bi-check-lg">Guardar empleado</x-button></div></form>
</x-card>
@endsection
