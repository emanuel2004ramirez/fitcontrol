@extends('layouts.app')
@section('title','Nuevo cliente')
@php
    $clavesError = array_keys($errors->toArray());
    $pasoInicial = collect($clavesError)->contains(fn ($clave) => str_starts_with($clave, 'consentimiento.')) ? 3
        : (collect($clavesError)->contains(fn ($clave) => str_starts_with($clave, 'medico.')) ? 2
        : (collect($clavesError)->contains(fn ($clave) => str_starts_with($clave, 'contacto.')) ? 1 : 0));
@endphp
@section('content')
<x-page-header title="Nuevo cliente" subtitle="Completa el expediente inicial en cuatro pasos."/>
<div class="text-center py-5"><x-button type="button" data-bs-toggle="modal" data-bs-target="#expedienteModal">Abrir expediente</x-button></div>

<div class="modal fade" id="expedienteModal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="expedienteTitulo">
<div class="modal-dialog modal-xl modal-dialog-scrollable"><div class="modal-content">
<form method="POST" action="{{ route('clientes.store') }}" id="expedienteForm">@csrf
<div class="modal-header"><div><h5 class="modal-title" id="expedienteTitulo">Crear expediente del cliente</h5><div class="small text-muted" id="pasoTexto">Paso 1 de 4 · Datos personales</div></div><a href="{{ route('clientes.index') }}" class="btn-close"></a></div>
<div class="progress rounded-0" style="height:4px"><div class="progress-bar" id="pasoBarra" style="width:25%"></div></div>
<div class="modal-body p-4">
@if($errors->any())
<div class="alert alert-danger" role="alert"><div class="fw-semibold mb-1"><i class="bi bi-exclamation-triangle me-2"></i>No se pudo crear el expediente</div><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif
<input type="hidden" name="estado_cliente_id" value="{{ collect($estados)->firstWhere('codigo','ACTIVO')->id ?? collect($estados)->first()->id }}">

<section class="wizard-step" data-title="Datos personales"><div class="row g-3">
<div class="col-md-6"><label class="form-label">Nombre</label><input class="form-control" name="nombre" value="{{ old('nombre') }}" required></div><div class="col-md-6"><label class="form-label">Apellido</label><input class="form-control" name="apellido" value="{{ old('apellido') }}" required></div>
<div class="col-md-4"><label class="form-label">Sexo</label><select class="form-select" name="sexo_id" required><option value="">Seleccione</option>@foreach($sexos as $s) @if($s->activo)<option value="{{ $s->id }}" @selected(old('sexo_id')==$s->id)>{{ $s->nombre }}</option>@endif @endforeach</select></div><div class="col-md-4"><label class="form-label">Nacimiento</label><input type="date" class="form-control" name="fecha_nacimiento" max="{{ now()->toDateString() }}" value="{{ old('fecha_nacimiento') }}" required></div><div class="col-md-4"><label class="form-label">Teléfono</label><input class="form-control" name="telefono" value="{{ old('telefono') }}" inputmode="numeric" pattern="[0-9]{8}" minlength="8" maxlength="8" data-digits-only title="Ingrese exactamente 8 números" required></div>
<div class="col-md-6"><label class="form-label">Correo</label><input type="email" class="form-control" name="correo_electronico" value="{{ old('correo_electronico') }}" required></div><div class="col-md-3"><label class="form-label">Tipo identificación</label><select class="form-select" name="tipo_identificacion" required><option value="">Seleccione</option>@foreach(['DNI'=>'DNI / Identidad','PASAPORTE'=>'Pasaporte','CARNET_RESIDENTE'=>'Carné de residente','OTRO'=>'Otro'] as $valor=>$texto)<option value="{{ $valor }}" @selected(old('tipo_identificacion')===$valor)>{{ $texto }}</option>@endforeach</select></div><div class="col-md-3"><label class="form-label">Identificación</label><input class="form-control" name="numero_identificacion" value="{{ old('numero_identificacion') }}" maxlength="60" required></div>
<div class="col-md-8"><label class="form-label">Dirección</label><input class="form-control" name="direccion" value="{{ old('direccion') }}" maxlength="200" required></div><div class="col-md-4"><label class="form-label">Ciudad</label><input class="form-control" name="ciudad" value="{{ old('ciudad') }}" maxlength="100" required></div>
</div></section>

<section class="wizard-step d-none" data-title="Contacto de emergencia"><div class="alert alert-info">Este contacto es obligatorio para completar el expediente.</div><div class="row g-3"><div class="col-md-5"><label class="form-label">Nombre completo</label><input class="form-control" name="contacto[nombre_completo]" value="{{ old('contacto.nombre_completo') }}" required></div><div class="col-md-3"><label class="form-label">Parentesco</label><input class="form-control" name="contacto[parentesco]" value="{{ old('contacto.parentesco') }}" required></div><div class="col-md-4"><label class="form-label">Teléfono</label><input class="form-control" name="contacto[telefono]" value="{{ old('contacto.telefono') }}" inputmode="numeric" pattern="[0-9]{8}" minlength="8" maxlength="8" data-digits-only title="Ingrese exactamente 8 números" required></div></div></section>

<section class="wizard-step d-none" data-title="Información médica"><div class="alert alert-warning"><i class="bi bi-shield-lock me-2"></i>Información confidencial. Después del registro solamente podrán verla roles autorizados.</div><div class="row g-3">@foreach(['condiciones_medicas'=>'Condiciones médicas','alergias'=>'Alergias','medicamentos'=>'Medicamentos','restricciones_ejercicio'=>'Restricciones para ejercicio'] as $campo=>$label)<div class="col-md-6"><label class="form-label">{{ $label }} <span class="text-muted">(opcional)</span></label><textarea class="form-control" name="medico[{{ $campo }}]" rows="3">{{ old('medico.'.$campo) }}</textarea></div>@endforeach<div class="col-12"><label class="form-label">Contacto médico <span class="text-muted">(opcional)</span></label><input class="form-control" name="medico[contacto_medico]" value="{{ old('medico.contacto_medico') }}"></div></div></section>

<section class="wizard-step d-none" data-title="Privacidad y confirmación"><div class="card bg-light border-0"><div class="card-body"><h6>Resumen del expediente</h6><p class="mb-2">Se generará automáticamente el número de socio, se guardará el contacto de emergencia y se enviará el correo de bienvenida.</p><div class="form-check"><input class="form-check-input" type="checkbox" name="consentimiento[aceptado]" value="1" id="aceptaPrivacidad" required @checked(old('consentimiento.aceptado'))><label class="form-check-label" for="aceptaPrivacidad">El cliente acepta el tratamiento de sus datos personales y médicos conforme a la política de privacidad versión 1.0.</label></div></div></div></section>
</div>
<div class="modal-footer"><button type="button" class="btn btn-outline-secondary d-none" id="anterior">Anterior</button><button type="button" class="btn btn-primary" id="siguiente">Siguiente</button><button type="submit" class="btn btn-success d-none" id="guardar">Crear expediente</button></div>
</form></div></div></div>
@push('scripts')<script>
document.addEventListener('DOMContentLoaded',()=>{const modal=new bootstrap.Modal('#expedienteModal');modal.show();document.querySelectorAll('[data-digits-only]').forEach(campo=>campo.addEventListener('input',()=>campo.value=campo.value.replace(/\D/g,'').slice(0,8)));const pasos=[...document.querySelectorAll('.wizard-step')],ant=document.getElementById('anterior'),sig=document.getElementById('siguiente'),guardar=document.getElementById('guardar'),texto=document.getElementById('pasoTexto'),barra=document.getElementById('pasoBarra');let actual={{ $pasoInicial }};function pintar(){pasos.forEach((p,i)=>p.classList.toggle('d-none',i!==actual));ant.classList.toggle('d-none',actual===0);sig.classList.toggle('d-none',actual===pasos.length-1);guardar.classList.toggle('d-none',actual!==pasos.length-1);texto.textContent=`Paso ${actual+1} de ${pasos.length} · ${pasos[actual].dataset.title}`;barra.style.width=`${((actual+1)/pasos.length)*100}%`;}sig.addEventListener('click',()=>{const campos=[...pasos[actual].querySelectorAll('input,select,textarea')];if(campos.some(c=>!c.reportValidity()))return;actual++;pintar();});ant.addEventListener('click',()=>{actual--;pintar();});pintar();});
</script>@endpush
@endsection
