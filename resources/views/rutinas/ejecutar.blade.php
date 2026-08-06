@extends('layouts.app')
@section('title','Registrar entrenamiento')
@section('content')
<x-page-header title="Registrar entrenamiento" :subtitle="$rutinaModelo->nombre" />
<form method="POST" action="{{ route('rutinas.ejecutar.guardar', $rutinaModelo->id) }}">@csrf
<input type="hidden" name="entrenamiento_id" value="{{ $entrenamiento->id }}">
@forelse($ejercicios as $detalle)
<x-card class="mb-3" :title="$detalle->ejercicio->nombre"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Serie</th><th>Repeticiones</th><th>Peso (kg)</th><th>Notas</th></tr></thead><tbody>
@for($numero=1; $numero <= max(1, $detalle->series ?? 3); $numero++) @php($serie=$series->get($detalle->id.'-'.$numero))
<tr><td>{{ $numero }}</td><td><input class="form-control" type="number" min="0" name="series[{{ $detalle->id }}][{{ $numero }}][repeticiones]" value="{{ old('series.'.$detalle->id.'.'.$numero.'.repeticiones', $serie?->repeticiones) }}"></td><td><input class="form-control" type="number" min="0" step=".01" name="series[{{ $detalle->id }}][{{ $numero }}][peso]" value="{{ old('series.'.$detalle->id.'.'.$numero.'.peso', $serie?->peso) }}"></td><td><input class="form-control" name="series[{{ $detalle->id }}][{{ $numero }}][notas]" value="{{ old('series.'.$detalle->id.'.'.$numero.'.notas', $serie?->notas) }}"></td></tr>
@endfor
</tbody></table></div></x-card>
@empty <x-card><div class="text-muted">No hay ejercicios configurados para esta rutina.</div></x-card>
@endforelse
<div class="text-end"><x-button type="submit">Guardar ejecución</x-button></div>
</form>
@endsection
