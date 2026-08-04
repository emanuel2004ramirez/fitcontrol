@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <x-page-header title="Buenos días" subtitle="Aquí tendrás una vista general de la operación de FitControl.">
        <span class="badge date-badge"><i class="bi bi-calendar3 me-1"></i>{{ now()->translatedFormat('d M, Y') }}</span>
    </x-page-header>

    <div class="row g-3 g-xl-4 mb-4">
        <div class="col-12 col-sm-6 col-xl-3"><x-stat-card label="Clientes activos" icon="bi-people" tone="primary" /></div>
        <div class="col-12 col-sm-6 col-xl-3"><x-stat-card label="Membresías vigentes" icon="bi-card-checklist" tone="success" /></div>
        <div class="col-12 col-sm-6 col-xl-3"><x-stat-card label="Asistencias hoy" icon="bi-person-check" tone="warning" /></div>
        <div class="col-12 col-sm-6 col-xl-3"><x-stat-card label="Ingresos del día" icon="bi-wallet2" tone="info" /></div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-xl-8">
            <x-card title="Actividad reciente" subtitle="Resumen de movimientos del gimnasio">
                <x-empty-state title="Dashboard listo" message="Los indicadores se mostrarán cuando conectemos esta interfaz con el módulo de reportes." icon="bi-bar-chart-line" />
            </x-card>
        </div>
        <div class="col-12 col-xl-4">
            <x-card title="Acciones rápidas" subtitle="Accesos frecuentes">
                <div class="quick-actions">
                    <button class="quick-action" disabled><span><i class="bi bi-person-plus"></i></span><div><strong>Nuevo cliente</strong><small>Próximamente</small></div><i class="bi bi-chevron-right"></i></button>
                    <button class="quick-action" disabled><span><i class="bi bi-qr-code-scan"></i></span><div><strong>Registrar acceso</strong><small>Próximamente</small></div><i class="bi bi-chevron-right"></i></button>
                    <button class="quick-action" disabled><span><i class="bi bi-receipt"></i></span><div><strong>Registrar pago</strong><small>Próximamente</small></div><i class="bi bi-chevron-right"></i></button>
                </div>
            </x-card>
        </div>
    </div>
@endsection
