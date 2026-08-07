@extends('layouts.app')
@section('title', $cliente->nombre_completo)
@php
    $permissions = session('permissions', []);
    $can = static fn (string $permission): bool => in_array('*', $permissions, true) || in_array($permission, $permissions, true);
    $breadcrumbs = [['label' => 'Clientes', 'url' => route('clientes.index')], ['label' => $cliente->nombre_completo]];
@endphp

@section('content')
<x-page-header :title="$cliente->nombre_completo" :subtitle="$cliente->numero_socio.' · '.$cliente->estado">
    @if($can('clientes.update'))
        <x-button :href="route('clientes.edit', $cliente->id)" variant="outline-primary" icon="bi-pencil">Editar</x-button>
    @endif
</x-page-header>

<div class="row g-4 mb-4">
    <div class="col-lg-7">
        <x-card title="Expediente del cliente">
            <div class="row g-3">
                <div class="col-sm-6"><small class="text-muted d-block">Estado</small><span class="badge text-bg-primary">{{ $cliente->estado }}</span></div>
                <div class="col-sm-6"><small class="text-muted d-block">Identificación</small>{{ trim(($cliente->tipo_identificacion ?? '').' '.($cliente->numero_identificacion ?? '')) ?: 'No registrada' }}</div>
                <div class="col-sm-6"><small class="text-muted d-block">Teléfono</small>{{ $cliente->telefono ?: 'No registrado' }}</div>
                <div class="col-sm-6"><small class="text-muted d-block">Correo</small>{{ $cliente->correo_electronico ?: 'No registrado' }}</div>
                <div class="col-sm-6"><small class="text-muted d-block">Nacimiento</small>{{ $cliente->fecha_nacimiento ? \Illuminate\Support\Carbon::parse($cliente->fecha_nacimiento)->format('d/m/Y') : 'No registrado' }}</div>
                <div class="col-sm-6"><small class="text-muted d-block">Ciudad</small>{{ $cliente->ciudad ?: 'No registrada' }}</div>
                <div class="col-12"><small class="text-muted d-block">Dirección</small>{{ $cliente->direccion ?: 'No registrada' }}</div>
            </div>
        </x-card>
    </div>
    <div class="col-lg-5">
        @if($can('clientes.changeStatus'))
            <x-card title="Cambiar estado">
                <form method="POST" action="{{ route('clientes.estado', $cliente->id) }}" class="row g-3">
                    @csrf @method('PATCH')
                    <div class="col-12"><select class="form-select" name="estado_cliente_id" required><option value="">Nuevo estado</option>@foreach($estados as $estado) @if($estado->activo && $estado->id != $cliente->estado_cliente_id)<option value="{{ $estado->id }}">{{ $estado->nombre }}</option>@endif @endforeach</select></div>
                    <div class="col-12"><textarea class="form-control" name="motivo" maxlength="255" placeholder="Motivo del cambio" required></textarea></div>
                    <div class="text-end"><x-button type="submit">Actualizar</x-button></div>
                </form>
            </x-card>
        @endif
    </div>
</div>

<ul class="nav nav-tabs mb-3">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#contactos">Contactos</button></li>
    @if($can('clientes.medical'))<li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#medicos">Datos médicos</button></li>@endif
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#consentimientos">Consentimientos</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#historial">Estados</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#linea-tiempo">Línea de tiempo</button></li>
</ul>

<div class="tab-content">
    <div class="tab-pane fade show active" id="contactos"><x-card title="Contactos de emergencia">
        @if($can('clientes.manage'))
            <form method="POST" action="{{ route('clientes.contactos.store', $cliente->id) }}" class="row g-3 border-bottom pb-4 mb-3">@csrf
                <input type="hidden" name="cliente_id" value="{{ $cliente->id }}">
                <div class="col-md-4"><input class="form-control" name="nombre_completo" placeholder="Nombre completo" required></div>
                <div class="col-md-3"><input class="form-control" name="parentesco" placeholder="Parentesco" required></div>
                <div class="col-md-3"><input class="form-control" name="telefono" placeholder="Teléfono" required></div>
                <div class="col-md-2"><input type="hidden" name="es_principal" value="0"><div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="es_principal" value="1" id="principal"><label class="form-check-label" for="principal">Principal</label></div><x-button type="submit" class="w-100">Agregar</x-button></div>
            </form>
        @endif
        <div class="table-responsive"><table class="table"><thead><tr><th>Nombre</th><th>Parentesco</th><th>Teléfono</th><th></th></tr></thead><tbody>
        @forelse($contactos as $contacto)<tr><td>{{ $contacto->nombre_completo }} @if($contacto->es_principal)<span class="badge text-bg-primary">Principal</span>@endif</td><td>{{ $contacto->parentesco }}</td><td>{{ $contacto->telefono }}</td><td class="text-end">@if($can('clientes.manage'))<form method="POST" action="{{ route('clientes.contactos.destroy', [$cliente->id, $contacto->id]) }}" onsubmit="return confirm('¿Eliminar este contacto?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>@endif</td></tr>@empty<tr><td colspan="4" class="text-center text-muted">Sin contactos.</td></tr>@endforelse
        </tbody></table></div>
    </x-card></div>

    @if($can('clientes.medical'))
        <div class="tab-pane fade" id="medicos"><x-card title="Información médica confidencial">
            <div class="alert alert-warning"><i class="bi bi-shield-lock me-2"></i>Acceso restringido. Esta información solo debe utilizarse para proteger la salud del cliente.</div>
            <form method="POST" action="{{ route('clientes.datos-medicos.update', $cliente->id) }}" class="row g-3">@csrf @method('PUT')
                <input type="hidden" name="cliente_id" value="{{ $cliente->id }}">
                @foreach(['condiciones_medicas' => 'Condiciones médicas', 'alergias' => 'Alergias', 'medicamentos' => 'Medicamentos', 'restricciones_ejercicio' => 'Restricciones de ejercicio'] as $campo => $label)
                    <div class="col-md-6"><label class="form-label">{{ $label }}</label><textarea class="form-control" name="{{ $campo }}" rows="3" maxlength="5000">{{ old($campo, $datosMedicos?->{$campo} ?? '') }}</textarea></div>
                @endforeach
                <div class="col-md-8"><label class="form-label">Contacto médico</label><input class="form-control" name="contacto_medico" maxlength="150" value="{{ old('contacto_medico', $datosMedicos?->contacto_medico) }}"></div>
                <div class="col-md-4 d-flex align-items-end"><x-button type="submit" class="w-100">Guardar ficha médica</x-button></div>
            </form>
        </x-card></div>
    @endif

    <div class="tab-pane fade" id="consentimientos"><x-card title="Consentimientos">
        <div class="table-responsive"><table class="table"><thead><tr><th>Fecha</th><th>Tipo</th><th>Versión</th><th>Decisión</th></tr></thead><tbody>@forelse($consentimientos as $consentimiento)<tr><td>{{ \Illuminate\Support\Carbon::parse($consentimiento->registrado_at)->format('d/m/Y H:i') }}</td><td>{{ $consentimiento->tipo }}</td><td>{{ $consentimiento->version_documento }}</td><td><span class="badge text-bg-{{ $consentimiento->aceptado ? 'success' : 'danger' }}">{{ $consentimiento->aceptado ? 'Aceptado' : 'Rechazado' }}</span></td></tr>@empty<tr><td colspan="4" class="text-center text-muted">Sin consentimientos.</td></tr>@endforelse</tbody></table></div>
    </x-card></div>

    <div class="tab-pane fade" id="historial"><x-card title="Historial de estados"><div class="table-responsive"><table class="table"><thead><tr><th>Fecha</th><th>Anterior</th><th>Nuevo</th><th>Motivo</th></tr></thead><tbody>@forelse($historialEstados as $item)<tr><td>{{ \Illuminate\Support\Carbon::parse($item->cambiado_at)->format('d/m/Y H:i') }}</td><td>{{ $item->estado_anterior ?: '—' }}</td><td><strong>{{ $item->estado_nuevo }}</strong></td><td>{{ $item->motivo ?: '—' }}</td></tr>@empty<tr><td colspan="4" class="text-center text-muted">Sin historial.</td></tr>@endforelse</tbody></table></div></x-card></div>

    <div class="tab-pane fade" id="linea-tiempo"><x-card title="Línea de tiempo unificada">
        <div class="list-group list-group-flush">@forelse($lineaTiempo as $evento)<div class="list-group-item px-0"><div class="d-flex justify-content-between gap-3"><div><strong>{{ $evento->titulo }}</strong><div class="text-muted">{{ $evento->descripcion ?: 'Sin detalle adicional' }}</div></div><small class="text-nowrap">{{ \Illuminate\Support\Carbon::parse($evento->ocurrido_at)->format('d/m/Y H:i') }}</small></div></div>@empty<div class="text-center text-muted py-4">Todavía no hay eventos registrados.</div>@endforelse</div>
    </x-card></div>
</div>

@if($can('clientes.delete'))
    <x-card title="Retirar cliente" class="border-danger mt-4"><form method="POST" action="{{ route('clientes.destroy', $cliente->id) }}" class="row g-3" onsubmit="return confirm('¿Retirar al cliente? Su historial se conservará.')">@csrf @method('DELETE')<div class="col-md-10"><input class="form-control" name="motivo" maxlength="255" placeholder="Motivo del retiro" required></div><div class="col-md-2"><x-button type="submit" variant="danger" class="w-100">Retirar</x-button></div></form></x-card>
@endif
@endsection
