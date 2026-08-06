@php
    $permissions = session('permissions', []);
    $hasPermission = static fn (string $permission): bool => in_array('*', $permissions, true) || in_array($permission, $permissions, true);
    $menu = [
        ['label' => 'Clientes', 'icon' => 'bi-people', 'permission' => 'clientes.viewAny', 'route' => 'clientes.index'],
        ['label' => 'Membresías', 'icon' => 'bi-card-checklist', 'permission' => 'membresias.viewAny', 'route' => 'membresias.index'],
        ['label' => 'Pagos', 'icon' => 'bi-wallet2', 'permission' => 'pagos.viewAny', 'route' => 'pagos.index'],
        ['label' => 'Asistencias', 'icon' => 'bi-qr-code-scan', 'permission' => 'asistencias.viewAny', 'route' => 'asistencias.index'],
        ['label' => 'Rutinas', 'icon' => 'bi-clipboard2-pulse', 'permission' => 'rutinas.viewAny', 'route' => 'rutinas.index'],
        ['label' => 'Evaluaciones', 'icon' => 'bi-activity', 'permission' => 'evaluaciones.viewAny', 'route' => 'evaluaciones.index'],
        ['label' => 'Ejercicios', 'icon' => 'bi-person-arms-up', 'permission' => 'ejercicios.viewAny', 'route' => 'ejercicios.index'],
        ['label' => 'Personal', 'icon' => 'bi-person-badge', 'permission' => 'personal.viewAny', 'route' => 'personal.index'],
        ['label' => 'Usuarios', 'icon' => 'bi-person-lock', 'permission' => 'usuarios.viewAny', 'route' => 'usuarios.index'],
        ['label' => 'Reportes', 'icon' => 'bi-bar-chart-line', 'permission' => 'reportes.viewAny', 'route' => 'reportes.index'],
        ['label' => 'Auditoría', 'icon' => 'bi-shield-lock', 'permission' => 'auditoria.viewAny', 'route' => 'auditoria.index'],
    ];
@endphp

<aside class="app-sidebar" id="appSidebar" aria-label="Navegación principal">
    <div class="sidebar-brand">
        <a href="{{ route('dashboard') }}" class="brand-link">
            <span class="brand-mark"><i class="bi bi-heart-pulse-fill"></i></span>
            <span><strong>Fit</strong>Control</span>
        </a>
        <button type="button" class="btn sidebar-close d-lg-none" data-sidebar-close aria-label="Cerrar menú"><i class="bi bi-x-lg"></i></button>
    </div>

    <nav class="sidebar-nav">
        <span class="sidebar-heading">Principal</span>
        <x-nav-item label="Dashboard" icon="bi-grid-1x2" :href="route('dashboard')" :active="request()->routeIs('dashboard')" />

        @if (collect($menu)->contains(fn ($item) => $hasPermission($item['permission'])))
            <span class="sidebar-heading mt-4">Gestión</span>
            @foreach ($menu as $item)
                @if ($hasPermission($item['permission']))
                    <x-nav-item
                        :label="$item['label']"
                        :icon="$item['icon']"
                        :href="Route::has($item['route']) ? route($item['route']) : '#'"
                        :active="request()->routeIs(Str::before($item['route'], '.') . '.*')"
                        :disabled="! Route::has($item['route'])"
                    />
                @endif
            @endforeach
        @endif
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-help"><i class="bi bi-shield-check"></i><div><strong>Sistema seguro</strong><small>Acceso según permisos</small></div></div>
    </div>
</aside>
