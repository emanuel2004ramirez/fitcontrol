<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cambiar contraseña · FitControl</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/image.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/image.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-body-tertiary">
<main class="min-vh-100 d-flex align-items-center justify-content-center p-3">
    <div class="card border-0 shadow-lg" style="width:min(100%,460px)">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <img src="{{ asset('images/image.svg') }}" alt="FitControl" class="fitcontrol-logo auth-logo mb-3">
                <h1 class="h3">Crea una nueva contraseña</h1>
                <p class="text-muted mb-0">Por seguridad, debes reemplazar la contraseña temporal antes de continuar.</p>
            </div>

            @if(session('warning'))<div class="alert alert-warning">{{ session('warning') }}</div>@endif
            @if($errors->any())<div class="alert alert-danger"><strong>Revisa la información ingresada.</strong><ul class="mb-0 mt-2">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

            <form method="POST" action="{{ route('password.change.update') }}">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label" for="password">Nueva contraseña</label>
                    <input id="password" type="password" name="password" class="form-control" minlength="6" maxlength="72" required autofocus autocomplete="new-password">
                    <div class="form-text">Debe tener al menos 6 caracteres y ser diferente de la contraseña temporal.</div>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="password_confirmation">Confirmar nueva contraseña</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" minlength="6" maxlength="72" required autocomplete="new-password">
                </div>
                <button class="btn btn-primary w-100 py-2" type="submit">Guardar y continuar</button>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="mt-3 text-center">
                @csrf
                <button type="submit" class="btn btn-link text-secondary">Cerrar sesión</button>
            </form>
        </div>
    </div>
</main>
</body>
</html>
