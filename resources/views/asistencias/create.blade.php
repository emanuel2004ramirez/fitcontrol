@extends('layouts.app')
@section('title','Registrar entrada')
@section('content')
<x-page-header title="Registrar entrada" subtitle="Registro manual para clientes vigentes y sin saldo pendiente."/>
<x-card>
    <form method="POST" action="{{ route('asistencias.store') }}" class="row g-3">
        @csrf
        <div class="col-12">
            <label class="form-label">Cliente</label>
            <select class="form-select" id="cliente" name="cliente_id" required>
                <option value="">Seleccione</option>
                @foreach($clientes as $c)
                    <option value="{{ $c->id }}" data-membresia="{{ $c->membresia_id }}">{{ $c->numero_socio }} · {{ $c->nombre }} · {{ $c->membresia }}</option>
                @endforeach
            </select>
            <input type="hidden" id="membresia" name="membresia_id">
            <input type="hidden" name="metodo_registro" value="MANUAL">
        </div>
        <div class="col-12">
            <label class="form-label">Observación <span class="text-muted">(opcional)</span></label>
            <textarea class="form-control" name="observaciones" rows="2" placeholder="Solo si necesita dejar una nota"></textarea>
        </div>
        <div class="text-end"><x-button type="submit">Registrar entrada</x-button></div>
    </form>
</x-card>
@push('scripts')
<script>document.getElementById('cliente').addEventListener('change',e=>document.getElementById('membresia').value=e.target.selectedOptions[0]?.dataset.membresia||'')</script>
@endpush
@endsection
