@php($editing = isset($personal))
<div class="row g-3">
    @if($editing)
        <div class="col-md-4"><label class="form-label">Código de empleado</label><input class="form-control" value="{{ $personal->codigo_empleado }}" disabled></div>
    @endif
    <div class="col-md-4"><label class="form-label" for="nombre">Nombre</label><input class="form-control" id="nombre" name="nombre" value="{{ old('nombre', $personal->nombre ?? '') }}" maxlength="100" required></div>
    <div class="col-md-4"><label class="form-label" for="apellido">Apellido</label><input class="form-control" id="apellido" name="apellido" value="{{ old('apellido', $personal->apellido ?? '') }}" maxlength="100" required></div>
    @unless($editing)
        <div class="col-md-4"><label class="form-label" for="cargo_id">Cargo inicial</label><select class="form-select" id="cargo_id" name="cargo_id" required><option value="">Seleccione</option>@foreach($cargos as $cargo)@if($cargo->activo)<option value="{{ $cargo->id }}" @selected(old('cargo_id') == $cargo->id)>{{ $cargo->nombre }}</option>@endif @endforeach</select></div>
        <div class="col-md-4"><label class="form-label" for="estado_personal_id">Estado inicial</label><select class="form-select" id="estado_personal_id" name="estado_personal_id" required><option value="">Seleccione</option>@foreach($estados as $estado)@if($estado->activo)<option value="{{ $estado->id }}" @selected(old('estado_personal_id') == $estado->id)>{{ $estado->nombre }}</option>@endif @endforeach</select></div>
        <div class="col-md-4"><label class="form-label" for="fecha_contratacion">Fecha de contratación</label><input type="date" class="form-control" id="fecha_contratacion" name="fecha_contratacion" value="{{ old('fecha_contratacion') }}" max="{{ now()->toDateString() }}" required></div>
    @endunless
    <div class="col-md-4"><label class="form-label" for="sexo_id">Sexo</label><select class="form-select" id="sexo_id" name="sexo_id"><option value="">No especificado</option>@foreach($sexos as $sexo)@if($sexo->activo)<option value="{{ $sexo->id }}" @selected(old('sexo_id', $personal->sexo_id ?? null) == $sexo->id)>{{ $sexo->nombre }}</option>@endif @endforeach</select></div>
    <div class="col-md-4"><label class="form-label" for="tipo_identificacion">Tipo de identificación</label><select class="form-select" id="tipo_identificacion" name="tipo_identificacion"><option value="">Sin identificación</option>@foreach(['DNI'=>'DNI / Identidad','PASAPORTE'=>'Pasaporte','CARNET_RESIDENTE'=>'Carné de residente','OTRO'=>'Otro'] as $valor=>$texto)<option value="{{ $valor }}" @selected(old('tipo_identificacion', $personal->tipo_identificacion ?? '') === $valor)>{{ $texto }}</option>@endforeach</select></div>
    <div class="col-md-4"><label class="form-label" for="numero_identificacion">Número de identificación</label><input class="form-control" id="numero_identificacion" name="numero_identificacion" value="{{ old('numero_identificacion', $personal->numero_identificacion ?? '') }}" maxlength="60"></div>
    <div class="col-md-6"><label class="form-label" for="telefono">Teléfono</label><input class="form-control" id="telefono" name="telefono" value="{{ old('telefono', $personal->telefono ?? '') }}" maxlength="25"></div>
    <div class="col-md-6"><label class="form-label" for="correo_electronico">Correo electrónico</label><input type="email" class="form-control" id="correo_electronico" name="correo_electronico" value="{{ old('correo_electronico', $personal->correo_electronico ?? '') }}" maxlength="150"></div>
</div>
