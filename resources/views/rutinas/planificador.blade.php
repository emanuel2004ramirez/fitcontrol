@extends('layouts.app')
@section('title', 'Rutinas')
@section('content')
<x-page-header title="Rutinas del cliente" subtitle="Busca un cliente y organiza sus rutinas y días de descanso durante la semana." />
<x-card :title="($esEntrenador ?? false) ? (($vistaClientes ?? 'sin-rutina') === 'mis-clientes' ? 'Mis clientes' : 'Clientes sin rutina') : 'Selecciona un cliente'" class="mb-4">
@if($esEntrenador ?? false)
<div class="btn-group mb-3" role="group" aria-label="Tipo de clientes">
    <a class="btn {{ ($vistaClientes ?? 'sin-rutina') === 'sin-rutina' ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('rutinas.index', ['vista' => 'sin-rutina']) }}">Clientes sin rutina</a>
    <a class="btn {{ ($vistaClientes ?? '') === 'mis-clientes' ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('rutinas.index', ['vista' => 'mis-clientes']) }}">Mis clientes</a>
</div>
@endif
<div class="row g-2 mb-3"><div class="col-md-8"><input id="buscarClienteRutina" class="form-control" value="{{ $busquedaCliente }}" placeholder="Buscar por ID, número de socio o nombre"></div></div>
<div class="row g-2" id="listaClientesRutina">
@forelse($clientes as $cliente)
<div class="col-md-6 col-xl-4 cliente-rutina" data-busqueda="{{ strtolower($cliente->id.' '.$cliente->numero_socio.' '.$cliente->nombre) }}">
    <a class="border rounded p-3 d-block h-100 text-decoration-none text-body" href="{{ route('rutinas.index', ['cliente_id' => $cliente->id, 'vista' => ($vistaClientes ?? null)]) }}"><strong>{{ $cliente->nombre }}</strong><div class="small text-muted">ID: {{ $cliente->id }} · Socio: {{ $cliente->numero_socio }}</div></a>
</div>
@empty
<div class="col"><span class="text-muted">{{ ($esEntrenador ?? false) && ($vistaClientes ?? '') === 'mis-clientes' ? 'Aún no tienes clientes con rutina asignada.' : 'No hay clientes sin rutina.' }}</span></div>
@endforelse
</div>
<div id="sinClientesRutina" class="text-muted text-center py-3 d-none">No se encontraron clientes.</div>
</x-card>
@if($clienteId)
<x-page-header :title="'Semana de '.$clienteSeleccionado->nombre" :subtitle="'Socio: '.$clienteSeleccionado->numero_socio" />
<form method="POST" action="{{ route('rutinas.planificador.guardar') }}">@csrf<input type="hidden" name="cliente_id" value="{{ $clienteId }}">
<x-card title="Plan semanal"><div class="row g-3">
@foreach(['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'] as $indice => $nombre)
@php($dia = $indice + 1) @php($asignacion = $plan->get($dia))
<div class="col-md-6 col-xl-4"><div class="border rounded p-3 h-100"><h6>{{ $nombre }}</h6>
@if($asignacion?->rutina_id)
<div class="small text-muted mb-2">Rutina asignada</div><a class="btn btn-outline-primary w-100 accion-rutina" href="{{ route('rutinas.show',$asignacion->rutina_id) }}">Editar rutina</a>@if(($esEntrenador ?? false) && ($vistaClientes ?? '') === 'mis-clientes')<a class="btn btn-success w-100 mt-2" href="{{ route('rutinas.ejecutar',$asignacion->rutina_id) }}">Registrar entrenamiento</a>@endif
@else
<div class="small text-muted mb-2">Sin rutina asignada</div><a class="btn btn-primary w-100 accion-rutina @if($asignacion?->es_descanso) disabled @endif" href="{{ route('rutinas.create',['cliente_id'=>$clienteId,'dia_semana'=>$dia]) }}" @if($asignacion?->es_descanso) aria-disabled="true" tabindex="-1" @endif>Crear rutina</a>
@endif
<label class="form-check mt-3"><input type="hidden" name="dias[{{ $indice }}][es_descanso]" value="0"><input class="form-check-input descanso-dia" type="checkbox" name="dias[{{ $indice }}][es_descanso]" value="1" @checked($asignacion?->es_descanso)><span class="form-check-label">Día de descanso</span></label>
</div></div>
@endforeach
</div><div class="text-end mt-4"><x-button type="submit">Guardar días de descanso</x-button></div></x-card></form>
@endif
@endsection
@push('scripts')
<script>
const buscarCliente=document.getElementById('buscarClienteRutina');
const filtrarClientes=()=>{const texto=buscarCliente.value.toLowerCase().trim();let total=0;document.querySelectorAll('.cliente-rutina').forEach(item=>{const visible=item.dataset.busqueda.includes(texto);item.hidden=!visible;if(visible)total++});document.getElementById('sinClientesRutina').classList.toggle('d-none',total>0)};
buscarCliente?.addEventListener('input',filtrarClientes);filtrarClientes();
document.querySelectorAll('.descanso-dia').forEach(c=>c.addEventListener('change',()=>{const boton=c.closest('.border').querySelector('.accion-rutina');if(!boton)return;boton.classList.toggle('disabled',c.checked);boton.setAttribute('aria-disabled',c.checked?'true':'false');boton.tabIndex=c.checked?-1:0;}));
</script>
@endpush
