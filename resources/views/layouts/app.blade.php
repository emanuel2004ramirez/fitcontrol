<!doctype html>
<html lang="es" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#2563eb">
    <title>@yield('title', 'Dashboard') · {{ config('app.name', 'FitControl') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/image.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/image.svg') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>
    <div class="app-shell">
        @include('partials.sidebar')

        <div class="app-main">
            @include('partials.navbar')

            <main class="app-content">
                <div class="container-fluid px-3 px-lg-4 py-4">
                    <x-breadcrumbs :items="$breadcrumbs ?? []" />
                    @include('partials.alerts')
                    @yield('content')
                </div>
            </main>

            @include('partials.footer')
        </div>
    </div>

    <div class="sidebar-backdrop" data-sidebar-close></div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
