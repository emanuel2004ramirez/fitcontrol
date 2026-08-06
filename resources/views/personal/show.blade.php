@extends('layouts.app')

@section('title', $personal->nombre_completo)

@php
    $permissions = session('permissions', []);
    $can = static fn (string $permission): bool => in_array('*', $permissions, true) || in_array($permission, $permissions, true);
    $breadcrumbs = [['label' => 'Personal', 'url' => route('personal.index')], ['label' => $personal->nombre_completo]];
@endphp

@section('content')
<x-page-header :title="$personal->nombre_completo" :subtitle="$personal->codigo_empleado . ' · ' . $personal->cargo">
    @if($can('personal.update'))<x-button :href="route('personal.edit', $personal->id)" variant="outline-primary" icon="bi-pencil">Editar datos</x-button>@endif
</x-page-header>

<div class="row g-4 mb-4">
    <div class="col-lg-7">
        <x-card title="Expediente">
            <div class="row g-3">
                <div class="col-sm-6"><small class="text-muted d-block">Estado</small><span class="badge text-bg-primary">{{ $personal->estado }}</span></div>
                <div class="col-sm-6"><small class="text-muted d-block">Cargo actual</small><strong>{{ $personal->cargo }}</strong></div>
                <div class="col-sm-6"><small class="text-muted d-block">Identificación</small>{{ trim(($personal->tipo_identificacion ?? '').' '.($personal->numero_identificacion ?? '')) ?: 'No registrada' }}</div>
                <div class="col-sm-6"><small class="text-muted d-block">Sexo</small>{{ $personal->sexo ?: 'No especificado' }}</div>
                <div class="col-sm-6"><small class="text-muted d-block">Teléfono</small>{{ $personal->telefono ?: 'No registrado' }}</div>
                <div class="col-sm-6"><small class="text-muted d-block">Correo</small>{{ $personal->correo_electronico ?: 'No registrado' }}</div>
                <div class="col-sm-6"><small class="text-muted d-block">Contratación</small>{{ \Illuminate\Support\Carbon::parse($personal->fecha_contratacion)->format('d/m/Y') }}</div>
            </div>
        </x-card>
    </div>
    <div class="col-lg-5">
        @if($can('personal.changeStatus'))
        <x-card title="Cambiar estado">
            <form method="POST" action="{{ route('personal.estado', $personal->id) }}" class="row g-3">@csrf @method('PATCH')
                <div class="col-12"><label class="form-label" for="estado_personal_id">Nuevo estado</label><select class="form-select" id="estado_personal_id" name="estado_personal_id" required><option value="">Seleccione</option>@foreach($estados as $estado)@if($estado->activo && $estado->id != $personal->estado_personal_id)<option value="{{ $estado->id }}">{{ $estado->nombre }}</option>@endif @endforeach</select></div>
                <div class="col-12"><label class="form-label" for="motivo_estado">Motivo</label><textarea class="form-control" id="motivo_estado" name="motivo" rows="2" maxlength="255" required></textarea></div>
                <div class="col-12 text-end"><x-button type="submit">Actualizar estado</x-button></div>
            </form>
        </x-card>
        @endif
    </div>
</div>

<ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#cargos" type="button">Historial de cargos</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#horarios" type="button">Horarios</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#evaluaciones" type="button">Evaluaciones</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#estados" type="button">Historial de estados</button></li>
</ul>

<div class="tab-content">
    <div class="tab-pane fade show active" id="cargos">
        <x-card title="Trayectoria laboral">
            @if($can('personal.manage'))
                <form method="POST" action="{{ route('personal.cargo', $personal->id) }}" class="row g-3 border-bottom pb-4 mb-3">@csrf
                    <div class="col-md-4"><label class="form-label" for="cargo_id">Nuevo cargo</label><select class="form-select" id="cargo_id" name="cargo_id" required><option value="">Seleccione</option>@foreach($cargos as $cargo)@if($cargo->activo && $cargo->id != $personal->cargo_id)<option value="{{ $cargo->id }}">{{ $cargo->nombre }}</option>@endif @endforeach</select></div>
                    <div class="col-md-3"><label class="form-label" for="vigente_desde_cargo">Vigente desde</label><input type="date" class="form-control" id="vigente_desde_cargo" name="vigente_desde" max="{{ now()->toDateString() }}" required></div>
                    <div class="col-md-3"><label class="form-label" for="motivo_cargo">Motivo</label><input class="form-control" id="motivo_cargo" name="motivo" maxlength="255" required></div>
                    <div class="col-md-2 d-flex align-items-end"><x-button type="submit" class="w-100">Asignar</x-button></div>
                </form>
            @endif
            <div class="table-responsive"><table class="table align-middle"><thead><tr><th>Cargo</th><th>Desde</th><th>Hasta</th><th>Motivo</th></tr></thead><tbody>@forelse($historialCargos as $item)<tr><td><strong>{{ $item->cargo }}</strong></td><td>{{ \Illuminate\Support\Carbon::parse($item->vigente_desde)->format('d/m/Y') }}</td><td>{{ $item->vigente_hasta ? \Illuminate\Support\Carbon::parse($item->vigente_hasta)->format('d/m/Y') : 'Actual' }}</td><td>{{ $item->motivo ?: '—' }}</td></tr>@empty<tr><td colspan="4" class="text-center text-muted py-4">Sin historial registrado.</td></tr>@endforelse</tbody></table></div>
        </x-card>
    </div>

    <div class="tab-pane fade" id="horarios">
        <x-card title="Horarios laborales">
            @if($can('personal.manage'))
                <form method="POST" action="{{ route('personal.horarios.store', $personal->id) }}" class="row g-3 border-bottom pb-4 mb-3">@csrf<input type="hidden" name="personal_id" value="{{ $personal->id }}">
                    <div class="col-md-2"><label class="form-label" for="dia_semana">Día</label><select class="form-select" id="dia_semana" name="dia_semana" required>@foreach(['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'] as $i => $dia)<option value="{{ $i + 1 }}">{{ $dia }}</option>@endforeach</select></div>
                    <div class="col-md-2"><label class="form-label" for="hora_inicio">Entrada</label><input type="time" class="form-control" id="hora_inicio" name="hora_inicio" required></div>
                    <div class="col-md-2"><label class="form-label" for="hora_fin">Salida</label><input type="time" class="form-control" id="hora_fin" name="hora_fin" required></div>
                    <div class="col-md-2"><label class="form-label" for="vigente_desde">Desde</label><input type="date" class="form-control" id="vigente_desde" name="vigente_desde" required></div>
                    <div class="col-md-2"><label class="form-label" for="vigente_hasta">Hasta</label><input type="date" class="form-control" id="vigente_hasta" name="vigente_hasta"></div>
                    <div class="col-md-2 d-flex align-items-end"><x-button type="submit" class="w-100">Agregar</x-button></div>
                </form>
            @endif
            <div class="table-responsive"><table class="table align-middle"><thead><tr><th>Día</th><th>Horario</th><th>Vigencia</th><th class="text-end">Acciones</th></tr></thead><tbody>
                @forelse($horarios as $horario)<tr><td>{{ $horario->dia }}</td><td>{{ substr($horario->hora_inicio, 0, 5) }} – {{ substr($horario->hora_fin, 0, 5) }}</td><td>{{ \Illuminate\Support\Carbon::parse($horario->vigente_desde)->format('d/m/Y') }} – {{ $horario->vigente_hasta ? \Illuminate\Support\Carbon::parse($horario->vigente_hasta)->format('d/m/Y') : 'Actual' }}</td><td class="text-end">
                    @if($can('personal.manage') && !$horario->vigente_hasta)
                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#editar-horario-{{ $horario->id }}"><i class="bi bi-pencil"></i></button>
                        <form method="POST" action="{{ route('personal.horarios.destroy', [$personal->id, $horario->id]) }}" class="d-inline" onsubmit="return confirm('¿Finalizar la vigencia de este horario?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" title="Finalizar"><i class="bi bi-calendar-x"></i></button></form>
                    @endif
                </td></tr>
                @if($can('personal.manage') && !$horario->vigente_hasta)<tr class="collapse" id="editar-horario-{{ $horario->id }}"><td colspan="4"><form method="POST" action="{{ route('personal.horarios.store', $personal->id) }}" class="row g-2">@csrf<input type="hidden" name="id" value="{{ $horario->id }}"><input type="hidden" name="personal_id" value="{{ $personal->id }}"><div class="col"><select class="form-select" name="dia_semana">@foreach(['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'] as $i => $dia)<option value="{{ $i + 1 }}" @selected($horario->dia_semana == $i + 1)>{{ $dia }}</option>@endforeach</select></div><div class="col"><input type="time" class="form-control" name="hora_inicio" value="{{ substr($horario->hora_inicio, 0, 5) }}" required></div><div class="col"><input type="time" class="form-control" name="hora_fin" value="{{ substr($horario->hora_fin, 0, 5) }}" required></div><div class="col"><input type="date" class="form-control" name="vigente_desde" value="{{ $horario->vigente_desde }}" required></div><div class="col"><input type="date" class="form-control" name="vigente_hasta" value="{{ $horario->vigente_hasta }}"></div><div class="col-auto"><x-button type="submit">Guardar</x-button></div></form></td></tr>@endif
                @empty<tr><td colspan="4" class="text-center text-muted py-4">Sin horarios registrados.</td></tr>@endforelse
            </tbody></table></div>
        </x-card>
    </div>

    <div class="tab-pane fade" id="evaluaciones">
        <x-card title="Evaluaciones de desempeno">
            @if($can('personal.manage'))
                <form method="POST" action="{{ route('personal.evaluaciones-desempeno.store', $personal->id) }}" class="row g-3 border-bottom pb-4 mb-3">@csrf
                    <div class="col-md-3"><label class="form-label" for="periodo_inicio">Periodo desde</label><input type="date" class="form-control" id="periodo_inicio" name="periodo_inicio" required></div>
                    <div class="col-md-3"><label class="form-label" for="periodo_fin">Periodo hasta</label><input type="date" class="form-control" id="periodo_fin" name="periodo_fin" required></div>
                    <div class="col-md-3"><label class="form-label" for="fecha_evaluacion">Fecha de evaluacion</label><input type="date" class="form-control" id="fecha_evaluacion" name="fecha_evaluacion" value="{{ now()->toDateString() }}" max="{{ now()->toDateString() }}" required></div>
                    <div class="col-md-3 d-flex align-items-end"><x-button type="submit" class="w-100">Registrar</x-button></div>
                    <div class="col-md-2"><label class="form-label" for="puntualidad">Puntualidad</label><select class="form-select" id="puntualidad" name="puntualidad" required>@for($i = 5; $i >= 1; $i--)<option value="{{ $i }}">{{ $i }}</option>@endfor</select></div>
                    <div class="col-md-2"><label class="form-label" for="responsabilidad">Responsabilidad</label><select class="form-select" id="responsabilidad" name="responsabilidad" required>@for($i = 5; $i >= 1; $i--)<option value="{{ $i }}">{{ $i }}</option>@endfor</select></div>
                    <div class="col-md-2"><label class="form-label" for="atencion_cliente">Atencion</label><select class="form-select" id="atencion_cliente" name="atencion_cliente" required>@for($i = 5; $i >= 1; $i--)<option value="{{ $i }}">{{ $i }}</option>@endfor</select></div>
                    <div class="col-md-2"><label class="form-label" for="trabajo_equipo">Equipo</label><select class="form-select" id="trabajo_equipo" name="trabajo_equipo" required>@for($i = 5; $i >= 1; $i--)<option value="{{ $i }}">{{ $i }}</option>@endfor</select></div>
                    <div class="col-md-2"><label class="form-label" for="rendimiento">Rendimiento</label><select class="form-select" id="rendimiento" name="rendimiento" required>@for($i = 5; $i >= 1; $i--)<option value="{{ $i }}">{{ $i }}</option>@endfor</select></div>
                    <div class="col-md-12"><label class="form-label" for="comentarios">Comentarios</label><textarea class="form-control" id="comentarios" name="comentarios" rows="2" maxlength="1000" placeholder="Observaciones del gerente o administrador"></textarea></div>
                </form>
            @endif
            <div class="table-responsive"><table class="table align-middle"><thead><tr><th>Periodo</th><th>Evaluador</th><th>Promedio</th><th>Estado</th><th>Comentarios</th><th class="text-end">Acciones</th></tr></thead><tbody>
                @forelse($evaluacionesDesempeno as $evaluacion)<tr><td>{{ \Illuminate\Support\Carbon::parse($evaluacion->periodo_inicio)->format('d/m/Y') }} - {{ \Illuminate\Support\Carbon::parse($evaluacion->periodo_fin)->format('d/m/Y') }}<div class="text-muted small">{{ \Illuminate\Support\Carbon::parse($evaluacion->fecha_evaluacion)->format('d/m/Y') }}</div></td><td>{{ $evaluacion->evaluador ?: 'No registrado' }}</td><td><span class="badge text-bg-{{ $evaluacion->promedio >= 4 ? 'success' : ($evaluacion->promedio >= 3 ? 'warning' : 'danger') }}">{{ number_format((float) $evaluacion->promedio, 2) }}/5</span></td><td><span class="badge text-bg-{{ $evaluacion->estado === 'aprobada' ? 'primary' : 'secondary' }}">{{ ucfirst($evaluacion->estado) }}</span></td><td>{{ $evaluacion->comentarios ?: 'Sin comentarios' }}</td><td class="text-end">
                    @if($can('personal.manage') && $evaluacion->estado !== 'aprobada')
                        <form method="POST" action="{{ route('personal.evaluaciones-desempeno.aprobar', [$personal->id, $evaluacion->id]) }}" class="d-inline" onsubmit="return confirm('Aprobar esta evaluacion de desempeno?')">@csrf @method('PATCH')<button class="btn btn-sm btn-outline-primary" title="Aprobar"><i class="bi bi-check2-circle"></i></button></form>
                    @elseif($evaluacion->aprobador)
                        <small class="text-muted">Aprobada por {{ $evaluacion->aprobador }}</small>
                    @endif
                </td></tr>@empty<tr><td colspan="6" class="text-center text-muted py-4">Sin evaluaciones registradas.</td></tr>@endforelse
            </tbody></table></div>
        </x-card>
    </div>

    <div class="tab-pane fade" id="estados">
        <x-card title="Cambios de estado"><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Fecha</th><th>Anterior</th><th>Nuevo</th><th>Motivo</th></tr></thead><tbody>@forelse($historialEstados as $item)<tr><td>{{ \Illuminate\Support\Carbon::parse($item->cambiado_at)->format('d/m/Y H:i') }}</td><td>{{ $item->estado_anterior ?: '—' }}</td><td><strong>{{ $item->estado_nuevo }}</strong></td><td>{{ $item->motivo ?: '—' }}</td></tr>@empty<tr><td colspan="4" class="text-center text-muted py-4">Sin historial registrado.</td></tr>@endforelse</tbody></table></div></x-card>
    </div>
</div>

@if($can('personal.delete'))
<x-card title="Retirar empleado" class="border-danger mt-4">
    <form method="POST" action="{{ route('personal.destroy', $personal->id) }}" class="row g-3" onsubmit="return confirm('¿Confirma el retiro del empleado? Se conservará todo su historial.')">@csrf @method('DELETE')
        <div class="col-md-3"><label class="form-label" for="fecha_terminacion">Fecha de terminación</label><input type="date" class="form-control" id="fecha_terminacion" name="fecha_terminacion" max="{{ now()->toDateString() }}"></div>
        <div class="col-md-7"><label class="form-label" for="motivo_retiro">Motivo</label><input class="form-control" id="motivo_retiro" name="motivo" maxlength="255" required></div>
        <div class="col-md-2 d-flex align-items-end"><x-button type="submit" variant="danger" class="w-100">Retirar</x-button></div>
    </form>
</x-card>
@endif
@endsection
