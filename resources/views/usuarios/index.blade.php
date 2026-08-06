@extends('layouts.app')
@section('title', 'Usuarios')
@php($permissions=session('permissions',[]))
@php($can=static fn(string $p):bool=>in_array('*',$permissions,true)||in_array($p,$permissions,true))
@section('content')
<x-page-header title="Usuarios del sistema" subtitle="Cuentas que pueden iniciar sesión en FitControl.">@if($can('usuarios.create'))<x-button :href="route('usuarios.create')" icon="bi-person-plus">Crear usuario</x-button>@endif</x-page-header>
<div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>Personal registra empleados. Usuarios administra quién puede ingresar, su contraseña y sus permisos.</div>
<x-card><div class="table-responsive"><table class="table table-hover align-middle"><thead><tr><th>Usuario</th><th>Empleado vinculado</th><th>Rol</th><th>Estado</th><th>Último acceso</th><th></th></tr></thead><tbody>
@forelse($usuarios as $usuario)<tr><td><strong>{{ $usuario->name }}</strong><div class="small text-muted">{{ '@'.$usuario->username }}{{ $usuario->email ? ' · '.$usuario->email : '' }}</div></td><td>{{ $usuario->empleado ?: 'Cuenta administrativa sin empleado' }}@if($usuario->cargo)<div class="small text-muted">{{ $usuario->cargo }}</div>@endif</td><td>{{ $usuario->roles ?: 'Sin rol' }}</td><td><span class="badge text-bg-{{ $usuario->activo ? 'success' : 'secondary' }}">{{ $usuario->activo ? 'Activo' : 'Inactivo' }}</span></td><td>{{ $usuario->ultimo_acceso_at ? \Illuminate\Support\Carbon::parse($usuario->ultimo_acceso_at)->format('d/m/Y H:i') : 'Nunca' }}</td><td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ route('usuarios.show',$usuario->id) }}"><i class="bi bi-eye"></i></a></td></tr>
@empty<tr><td colspan="6" class="text-center py-5 text-muted">No hay usuarios registrados.</td></tr>@endforelse
</tbody></table></div></x-card>
@endsection
