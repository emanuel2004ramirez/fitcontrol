@extends('layouts.app')
@section('title',$rutina->nombre)
@section('content')
<x-page-header :title="$rutina->nombre" :subtitle="$rutina->cliente.' · Entrenador: '.$rutina->entrenador"/>

<x-card title="Ejercicios de la rutina">
    <div class="table-responsive mb-4">
        <table class="table align-middle">
            <thead><tr><th>#</th><th>Ejercicio</th><th>Series</th><th>Repeticiones</th><th>Peso</th><th>Descanso</th><th></th></tr></thead>
            <tbody>
            @forelse(collect($contenido)->whereNotNull('detalle_id') as $x)
                <tr>
                    <td>{{ $x->orden }}</td><td><strong>{{ $x->ejercicio }}</strong>@if($x->indicaciones)<div class="small text-muted">{{ $x->indicaciones }}</div>@endif</td>
                    <td>{{ $x->series }}</td><td>{{ $x->repeticiones_min }}@if($x->repeticiones_max && $x->repeticiones_max != $x->repeticiones_min)–{{ $x->repeticiones_max }}@endif</td>
                    <td>{{ $x->peso ? $x->peso.' kg' : '—' }}</td><td>{{ $x->descanso_segundos ? $x->descanso_segundos.' s' : '—' }}</td>
                    <td><form method="POST" action="{{ route('rutinas.ejercicios.destroy',[$rutina->id,$x->detalle_id]) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" title="Quitar"><i class="bi bi-trash"></i></button></form></td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-4">Todavía no se han agregado ejercicios.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="border rounded p-3 bg-light">
        <h6 class="mb-3">Agregar ejercicio</h6>
        <form method="POST" action="{{ route('rutinas.ejercicios.store',$rutina->id) }}" class="row g-2 align-items-end">@csrf
            <div class="col-lg-4"><label class="form-label">Ejercicio</label><select class="form-select" name="ejercicio_id" required><option value="">Seleccione</option>@foreach($ejercicios as $e)<option value="{{ $e->id }}">{{ $e->nombre }}</option>@endforeach</select></div>
            <div class="col-6 col-lg-2"><label class="form-label">Series</label><input type="number" min="1" class="form-control" name="series" value="3" required></div>
            <div class="col-6 col-lg-2"><label class="form-label">Repeticiones</label><input type="number" min="1" class="form-control" name="repeticiones_min" value="10" required></div>
            <div class="col-6 col-lg-1"><label class="form-label">Peso</label><input type="number" min="0" step=".01" class="form-control" name="peso"></div>
            <div class="col-6 col-lg-2"><label class="form-label">Descanso (s)</label><input type="number" min="0" class="form-control" name="descanso_segundos" value="60"></div>
            <div class="col-lg-1"><x-button type="submit" class="w-100" title="Agregar ejercicio">+</x-button></div>
            <div class="col-12"><label class="form-label">Indicación <span class="text-muted">(opcional)</span></label><input class="form-control" name="indicaciones" placeholder="Técnica, velocidad o recomendación"></div>
        </form>
    </div>
</x-card>
@endsection
