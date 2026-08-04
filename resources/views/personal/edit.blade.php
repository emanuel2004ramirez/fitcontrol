@extends('layouts.app')
@section('title', 'Editar empleado')
@php($breadcrumbs = [['label' => 'Personal', 'url' => route('personal.index')], ['label' => $personal->nombre_completo, 'url' => route('personal.show', $personal->id)], ['label' => 'Editar']])
@section('content')
<x-page-header title="Editar empleado" subtitle="El cargo y el estado se gestionan desde el expediente para conservar su historial." />
<x-card>
    <form method="POST" action="{{ route('personal.update', $personal->id) }}">@csrf @method('PUT') @include('personal._form')<div class="d-flex justify-content-end gap-2 mt-4"><x-button :href="route('personal.show', $personal->id)" variant="outline-secondary">Cancelar</x-button><x-button type="submit" icon="bi-check-lg">Guardar cambios</x-button></div></form>
</x-card>
@endsection
