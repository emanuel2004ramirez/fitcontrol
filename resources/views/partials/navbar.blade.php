@php($currentUser = session('auth_user', ['name' => 'Invitado', 'role' => 'Sin sesión']))
<header class="app-navbar navbar navbar-expand">
    <div class="container-fluid px-3 px-lg-4">
        <button class="btn btn-icon d-lg-none me-2" type="button" data-sidebar-toggle aria-label="Abrir menú"><i class="bi bi-list fs-4"></i></button>
        <div class="d-none d-md-block">
            <span class="navbar-eyebrow">FitControl</span>
            <div class="navbar-page-title">@yield('page-title', 'Panel de control')</div>
        </div>

        <div class="navbar-actions ms-auto">
            <button class="btn btn-icon" type="button" data-theme-toggle title="Cambiar tema" aria-label="Cambiar tema"><i class="bi bi-moon-stars"></i></button>

            <div class="dropdown">
                <button class="btn user-menu dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="user-avatar">{{ mb_strtoupper(mb_substr($currentUser['name'] ?? 'I', 0, 1)) }}</span>
                    <span class="d-none d-sm-block text-start"><strong>{{ $currentUser['name'] ?? 'Invitado' }}</strong><small>{{ $currentUser['role'] ?? 'Sin sesión' }}</small></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                    <li><span class="dropdown-header">Cuenta</span></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Mi perfil</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Preferencias</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><form method="POST" action="{{ route('logout') }}">@csrf<button class="dropdown-item text-danger" type="submit"><i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión</button></form></li>
                </ul>
            </div>
        </div>
    </div>
</header>
