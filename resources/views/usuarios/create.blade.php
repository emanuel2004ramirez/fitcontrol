@extends('layouts.app')
@section('title', 'Crear usuario')
@section('content')
<x-page-header title="Crear usuario de acceso" subtitle="Define las credenciales y el rol con el que ingresará al sistema."/>
<x-card><form method="POST" action="{{ route('usuarios.store') }}" class="row g-3">@csrf
    <div class="col-md-6"><label class="form-label">Empleado vinculado <span class="text-muted">(opcional)</span></label><select class="form-select" name="personal_id"><option value="">Cuenta administrativa sin empleado</option>@foreach($personal as $empleado)<option value="{{ $empleado->id }}" @selected(old('personal_id')==$empleado->id)>{{ $empleado->codigo_empleado }} · {{ $empleado->nombre }} · {{ $empleado->cargo }}</option>@endforeach</select><div class="form-text">Vincúlalo cuando la cuenta pertenece a un empleado. Un empleado solo puede tener una cuenta.</div></div>
    <div class="col-md-6"><label class="form-label">Rol</label><select class="form-select" name="rol_id" required><option value="">Seleccione los permisos iniciales</option>@foreach($roles as $rol) @if($rol->activo)<option value="{{ $rol->id }}" @selected(old('rol_id')==$rol->id)>{{ $rol->nombre }}</option>@endif @endforeach</select></div>
    <div class="col-md-6"><label class="form-label">Nombre visible</label><input class="form-control" name="name" value="{{ old('name') }}" maxlength="255" required></div>
    <div class="col-md-6"><label class="form-label">Nombre de usuario</label><input class="form-control" name="username" value="{{ old('username') }}" minlength="3" maxlength="60" autocomplete="off" required><div class="form-text">Se utilizará para iniciar sesión.</div></div>
    <div class="col-md-6"><label class="form-label">Correo <span class="text-muted">(opcional)</span></label><input type="email" class="form-control" name="email" value="{{ old('email') }}" maxlength="150"></div>
    <div class="col-md-6"><label class="form-label">Contraseña temporal</label><input type="password" class="form-control" name="password" minlength="6" maxlength="72" autocomplete="new-password" required></div>
    <div class="col-md-6"><label class="form-label">Confirmar contraseña</label><input type="password" class="form-control" name="password_confirmation" minlength="6" maxlength="72" autocomplete="new-password" required></div>
    <div class="col-md-6 d-flex align-items-end"><div class="form-check"><input type="hidden" name="debe_cambiar_password" value="0"><input class="form-check-input" type="checkbox" name="debe_cambiar_password" value="1" id="cambiar" checked><label class="form-check-label" for="cambiar">Solicitar cambio de contraseña al usuario</label></div></div>
    <div class="col-12 text-end"><x-button :href="route('usuarios.index')" variant="outline-secondary" class="me-2">Cancelar</x-button><x-button type="submit">Crear cuenta</x-button></div>
</form></x-card>
@endsection
