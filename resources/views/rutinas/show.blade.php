@extends('layouts.app')
@section('title', $rutina->nombre)
@section('content')
@php($ejerciciosRutina = collect($contenido)->whereNotNull('detalle_id'))
@php($catalogoEjercicios = collect($ejercicios))
<x-page-header :title="$rutina->nombre" :subtitle="$rutina->cliente.' · Entrenador: '.$rutina->entrenador" />

<x-card title="Ejercicios de la rutina">
    <div class="d-flex justify-content-between align-items-center mb-3"><span class="text-muted">Arrastra un ejercicio para cambiar su posición y guarda sus parámetros directamente.</span><button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#constructorRutina"><i class="bi bi-plus-lg"></i> Gestionar ejercicios</button></div>
    <div id="lista-ejercicios">
    @forelse($ejerciciosRutina as $x)
        <div class="card mb-3 ejercicio-item" draggable="true" data-id="{{ $x->detalle_id }}"><form method="POST" action="{{ route('rutinas.ejercicios.update',[$rutina->id,$x->detalle_id]) }}" class="card-body row g-2 align-items-end">@csrf @method('PUT')
            <div class="col-12 d-flex gap-2 align-items-center"><span class="btn btn-sm btn-light">☰</span><strong>{{ $x->ejercicio }}</strong><span class="badge text-bg-light">Posición {{ $x->orden }}</span><button class="btn btn-sm btn-outline-danger ms-auto" formmethod="POST" formaction="{{ route('rutinas.ejercicios.destroy',[$rutina->id,$x->detalle_id]) }}" name="_method" value="DELETE"><i class="bi bi-trash"></i> Quitar</button></div>
            @foreach(['series'=>'Series','repeticiones_min'=>'Reps mín.','repeticiones_max'=>'Reps máx.','descanso_segundos'=>'Descanso (s)'] as $field=>$label)<div class="col-6 col-md-3 col-xl"><label class="form-label small">{{ $label }}</label><input class="form-control form-control-sm" name="{{ $field }}" value="{{ $x->$field }}" @if(in_array($field,['series','repeticiones_min','repeticiones_max','descanso_segundos'])) type="number" min="0" @endif></div>@endforeach
            <div class="col-12 col-lg-8"><label class="form-label small">Indicaciones</label><input class="form-control form-control-sm" name="indicaciones" value="{{ $x->indicaciones }}"></div><div class="col"><button class="btn btn-sm btn-outline-primary w-100">Guardar</button></div>
        </form></div>
    @empty <div class="text-center text-muted py-5">No hay ejercicios agregados.</div> @endforelse
    </div>
</x-card>

<div class="modal fade" id="constructorRutina" tabindex="-1"><div class="modal-dialog modal-xl modal-dialog-scrollable"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Gestionar ejercicios</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body"><div class="row g-4">
        <section class="col-lg-5 border-end"><h6>Agregados a esta rutina <span class="badge text-bg-primary">{{ $ejerciciosRutina->count() }}</span></h6><p class="small text-muted">Quita un ejercicio aquí; para reordenarlos, arrástralos en la lista principal.</p><div class="list-group">@forelse($ejerciciosRutina as $x)<div class="list-group-item d-flex justify-content-between align-items-center"><span><strong>{{ $x->orden }}.</strong> {{ $x->ejercicio }}</span><form method="POST" action="{{ route('rutinas.ejercicios.destroy',[$rutina->id,$x->detalle_id]) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Quitar</button></form></div>@empty <div class="text-muted">Aún no agregaste ejercicios.</div>@endforelse</div></section>
        <section class="col-lg-7"><h6>Catálogo de ejercicios disponibles</h6><div class="row g-2 mb-3"><div class="col-md-5"><input id="buscarEjercicio" class="form-control" placeholder="Nombre del ejercicio"></div><div class="col-md-3"><select id="filtroGrupo" class="form-select"><option value="">Grupo muscular</option>@foreach($gruposMusculares as $grupo)<option value="{{ $grupo->nombre }}">{{ $grupo->nombre }}</option>@endforeach</select></div><div class="col-md-3"><select id="filtroEquipo" class="form-select"><option value="">Equipamiento</option>@foreach($equipamientosRutina as $equipo)<option value="{{ $equipo->nombre }}">{{ $equipo->nombre }}</option>@endforeach</select></div><div class="col-md-1"><button id="botonBuscar" type="button" class="btn btn-outline-primary w-100" title="Buscar"><i class="bi bi-search"></i></button></div></div><div id="sinResultados" class="alert alert-light d-none">No hay ejercicios que coincidan con los filtros.</div><div class="row g-2" id="catalogoLista">@foreach($catalogoEjercicios as $e)<div class="col-md-6 catalogo-item" data-name="{{ strtolower($e->nombre) }}" data-group="{{ strtolower($e->grupos_musculares ?? '') }}" data-equipment="{{ strtolower($e->equipamiento ?? '') }}"><label class="border rounded p-3 w-100 h-100"><input class="form-check-input me-2" type="checkbox" form="agregarLote" name="ejercicio_ids[]" value="{{ $e->id }}"><strong>{{ $e->nombre }}</strong><div class="small text-muted">{{ ($e->grupos_musculares??null)?:'Sin grupo' }} · {{ ($e->equipamiento??null)?:'Peso corporal' }}</div></label></div>@endforeach</div></section>
    </div></div><div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button><form id="agregarLote" method="POST" action="{{ route('rutinas.ejercicios.batch',$rutina->id) }}">@csrf<button class="btn btn-primary">Agregar seleccionados</button></form></div>
</div></div></div>
<form id="ordenForm" method="POST" action="{{ route('rutinas.ejercicios.order',$rutina->id) }}" class="d-none">@csrf @method('PUT')<span id="ordenInputs"></span></form>
@push('scripts')<script>const list=document.getElementById('lista-ejercicios');let drag;list?.addEventListener('dragstart',e=>drag=e.target.closest('.ejercicio-item'));list?.addEventListener('dragover',e=>{e.preventDefault();const t=e.target.closest('.ejercicio-item');if(t&&t!==drag)list.insertBefore(drag,t)});list?.addEventListener('dragend',()=>{document.getElementById('ordenInputs').innerHTML=[...list.querySelectorAll('.ejercicio-item')].map(x=>`<input name="detalles[]" value="${x.dataset.id}">`).join('');document.getElementById('ordenForm').submit()});const filtrar=()=>{const q=document.getElementById('buscarEjercicio').value.toLowerCase(),g=document.getElementById('filtroGrupo').value.toLowerCase(),e=document.getElementById('filtroEquipo').value.toLowerCase();let n=0;document.querySelectorAll('.catalogo-item').forEach(x=>{const ok=x.dataset.name.includes(q)&&(!g||x.dataset.group.includes(g))&&(!e||x.dataset.equipment.includes(e));x.hidden=!ok;if(ok)n++});document.getElementById('sinResultados').classList.toggle('d-none',n>0)};document.getElementById('botonBuscar')?.addEventListener('click',filtrar);document.getElementById('buscarEjercicio')?.addEventListener('keydown',e=>{if(e.key==='Enter'){e.preventDefault();filtrar()}});document.getElementById('filtroGrupo')?.addEventListener('change',filtrar);document.getElementById('filtroEquipo')?.addEventListener('change',filtrar);</script>@endpush
@endsection
@push('scripts')
<script>
// Conserva temporalmente los cambios de las otras tarjetas cuando se guarda un ejercicio.
const borradorRutinaKey = 'fitcontrol-rutina-borrador-{{ $rutina->id }}';
const borradorRutina = JSON.parse(sessionStorage.getItem(borradorRutinaKey) || '{}');
document.querySelectorAll('#lista-ejercicios input[name]').forEach(campo => {
    if (borradorRutina[campo.name] !== undefined) campo.value = borradorRutina[campo.name];
    campo.addEventListener('input', () => {
        borradorRutina[campo.name] = campo.value;
        sessionStorage.setItem(borradorRutinaKey, JSON.stringify(borradorRutina));
    });
});
</script>
@endpush
