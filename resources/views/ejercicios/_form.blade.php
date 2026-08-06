@php($editing=isset($ejercicio))
<div class="row g-3">
    <div class="col-md-6"><label class="form-label">Nombre</label><input class="form-control" name="nombre" value="{{ old('nombre',$ejercicio->nombre??'') }}" required></div>
    <div class="col-md-6"><label class="form-label">Estado</label><select class="form-select" name="estado_ejercicio_id" required>@foreach($estados as $e)<option value="{{ $e->id }}" @selected(old('estado_ejercicio_id',$ejercicio->estado_ejercicio_id??null)==$e->id)>{{ $e->nombre }}</option>@endforeach</select></div>
    <div class="col-md-6"><label class="form-label">Patrón de movimiento <span class="text-muted">(opcional)</span></label><input class="form-control" name="patron_movimiento" value="{{ old('patron_movimiento',$ejercicio->patron_movimiento??'') }}"></div>
    <div class="col-md-6"><label class="form-label">Equipamiento <span class="text-muted">(opcional)</span></label><input class="form-control" name="equipamiento" value="{{ old('equipamiento',$ejercicio->equipamiento??'') }}"></div>
    <div class="col-12"><label class="form-label">Descripción <span class="text-muted">(opcional)</span></label><textarea class="form-control" name="descripcion">{{ old('descripcion',$ejercicio->descripcion??'') }}</textarea></div>
    <div class="col-12"><label class="form-label">Instrucciones <span class="text-muted">(opcional)</span></label><textarea class="form-control" rows="5" name="instrucciones">{{ old('instrucciones',$ejercicio->instrucciones??'') }}</textarea></div>
    <div class="col-12"><label class="form-label">Video <span class="text-muted">(opcional)</span></label><input type="url" class="form-control" name="video_url" value="{{ old('video_url',$ejercicio->video_url??'') }}"></div>
</div>
