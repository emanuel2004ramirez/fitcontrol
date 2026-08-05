<!doctype html>
<html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="csrf-token" content="{{ csrf_token() }}"><title>Iniciar sesión · FitControl</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"><link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet"></head>
<body class="bg-body-tertiary"><main class="min-vh-100 d-flex align-items-center justify-content-center p-3"><div class="card border-0 shadow-lg" style="width:min(100%,430px)"><div class="card-body p-4 p-md-5">
<div class="text-center mb-4"><span class="d-inline-flex align-items-center justify-content-center rounded-4 bg-primary text-white mb-3" style="width:64px;height:64px"><i class="bi bi-heart-pulse-fill fs-2"></i></span><h1 class="h3">Bienvenido a FitControl</h1><p class="text-muted">Ingrese sus credenciales para continuar.</p></div>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
<form method="POST" action="{{ route('login.store') }}">@csrf
<div class="mb-3"><label class="form-label" for="login">Usuario o correo</label><div class="input-group"><span class="input-group-text"><i class="bi bi-person"></i></span><input id="login" name="login" class="form-control" value="{{ old('login') }}" required autofocus autocomplete="username"></div></div>
<div class="mb-4"><label class="form-label" for="password">Contraseña</label><div class="input-group"><span class="input-group-text"><i class="bi bi-lock"></i></span><input id="password" type="password" name="password" class="form-control" required autocomplete="current-password"></div></div>
<button class="btn btn-primary w-100 py-2" type="submit">Iniciar sesión</button></form>
</div></div></main></body></html>
