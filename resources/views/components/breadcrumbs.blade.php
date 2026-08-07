@props(['items' => []])
@php
    $homeItem = ['label' => 'Inicio', 'url' => route('dashboard')];
    $routeName = request()->route()?->getName() ?? '';
    $routePrefix = explode('.', $routeName)[0] ?? '';
    $moduleRoutes = [
        'asistencias' => ['label' => 'Asistencias', 'route' => 'asistencias.index'],
        'auditoria' => ['label' => 'Auditoría', 'route' => 'auditoria.index'],
        'cargos-cobro' => ['label' => 'Cargos por cobrar', 'route' => 'cargos-cobro.index'],
        'clientes' => ['label' => 'Clientes', 'route' => 'clientes.index'],
        'ejercicios' => ['label' => 'Ejercicios', 'route' => 'ejercicios.index'],
        'evaluaciones' => ['label' => 'Evaluaciones físicas', 'route' => 'evaluaciones.index'],
        'membresias' => ['label' => 'Membresías', 'route' => 'membresias.index'],
        'pagos' => ['label' => 'Pagos', 'route' => 'pagos.index'],
        'personal' => ['label' => 'Personal', 'route' => 'personal.index'],
        'reportes' => ['label' => 'Reportes', 'route' => 'reportes.index'],
        'rutinas' => ['label' => 'Rutinas', 'route' => 'rutinas.index'],
        'usuarios' => ['label' => 'Usuarios', 'route' => 'usuarios.index'],
    ];
    $currentLabel = trim($__env->yieldContent('title', 'Dashboard'));
    $resolvedItems = $items;

    if (empty($resolvedItems)) {
        $module = $moduleRoutes[$routePrefix] ?? null;
        $resolvedItems = [];

        if ($module) {
            $resolvedItems[] = ['label' => $module['label'], 'url' => route($module['route'])];
        }

        if (! $module || strcasecmp($currentLabel, $module['label']) !== 0) {
            $resolvedItems[] = ['label' => $currentLabel];
        }
    }

    if (isset($moduleRoutes[$routePrefix], $resolvedItems[0]) && ! isset($resolvedItems[0]['url'])) {
        $resolvedItems[0]['url'] = route($moduleRoutes[$routePrefix]['route']);
    }

    if (($resolvedItems[0]['label'] ?? null) !== 'Inicio') {
        array_unshift($resolvedItems, $homeItem);
    }
@endphp
<nav aria-label="Breadcrumb" class="mb-3">
    <ol class="breadcrumb app-breadcrumb mb-0">
        @foreach ($resolvedItems as $item)
            <li @class(['breadcrumb-item', 'active' => $loop->last]) @if($loop->last) aria-current="page" @endif>
                @if (! $loop->last && isset($item['url']))<a href="{{ $item['url'] }}">{{ $item['label'] }}</a>@else{{ $item['label'] }}@endif
            </li>
        @endforeach
    </ol>
</nav>
