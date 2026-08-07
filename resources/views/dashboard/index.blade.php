@extends('layouts.app')
@section('title','Dashboard')
@section('content')
<x-page-header title="Dashboard" subtitle="Resumen operativo y financiero de FitControl."><span class="badge date-badge"><i class="bi bi-calendar3 me-1"></i>{{ now()->translatedFormat('d M, Y') }}</span></x-page-header>
<div class="row g-3 g-xl-4 mb-4">
 <div class="col-6 col-xl-3"><x-stat-card label="Clientes" :value="$indicadores->clientes_registrados" icon="bi-people"/></div>
 <div class="col-6 col-xl-3"><x-stat-card label="Membresías activas" :value="$indicadores->membresias_activas" icon="bi-card-checklist" tone="success"/></div>
 <div class="col-6 col-xl-3"><x-stat-card label="Asistencias hoy" :value="$indicadores->asistencias_hoy" icon="bi-person-check" tone="warning"/></div>
 <div class="col-6 col-xl-3"><x-stat-card label="Personas dentro" :value="$indicadores->personas_dentro" icon="bi-door-open" tone="info"/></div>
 <div class="col-6 col-xl-3"><x-stat-card label="Ingresos hoy" :value="number_format($indicadores->ingresos_hoy,2).' HNL'" icon="bi-cash" tone="success"/></div>
 <div class="col-6 col-xl-3"><x-stat-card label="Ingresos del mes" :value="number_format($indicadores->ingresos_mes,2).' HNL'" icon="bi-graph-up-arrow"/></div>
 <div class="col-6 col-xl-3"><x-stat-card label="Membresías por vencer" :value="$indicadores->membresias_por_vencer" icon="bi-calendar-event" tone="info"/></div>
</div>
<div class="row g-4 mb-4"><div class="col-xl-7"><x-card title="Asistencias" subtitle="Visitas y clientes únicos durante los últimos 14 días"><div class="dashboard-chart"><canvas id="attendanceChart"></canvas></div></x-card></div><div class="col-xl-5"><x-card title="Ingresos" subtitle="Pagos registrados durante los últimos 14 días"><div class="dashboard-chart"><canvas id="revenueChart"></canvas></div></x-card></div></div>
<div class="row g-4"><div class="col-xl-4"><x-card title="Membresías activas por plan"><div class="dashboard-chart dashboard-chart-sm"><canvas id="membershipChart"></canvas></div></x-card></div><div class="col-xl-4"><x-card title="Actividad reciente"><div class="activity-feed">@forelse($actividad as $item)<div class="activity-item"><span><i class="bi {{ $item->icono }}"></i></span><div><strong>{{ $item->descripcion }}</strong><small>{{ \Illuminate\Support\Carbon::parse($item->fecha)->diffForHumans() }}</small></div></div>@empty<p class="text-muted">Sin actividad reciente.</p>@endforelse</div></x-card></div><div class="col-xl-4"><x-card title="Alertas" subtitle="Membresías que vencen en los próximos 7 días">@forelse($alertas as $alerta)<div class="alert-item"><i class="bi bi-exclamation-circle"></i><div><strong>{{ $alerta->cliente }}</strong><small>{{ $alerta->mensaje }}</small></div></div>@empty<div class="text-center text-muted py-5"><i class="bi bi-check-circle fs-2 d-block text-success"></i>Sin alertas pendientes.</div>@endforelse</x-card></div></div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.9/dist/chart.umd.min.js"></script>
<script>
const css=getComputedStyle(document.documentElement),grid=css.getPropertyValue('--fc-border').trim()||'#e2e8f0',text=css.getPropertyValue('--fc-muted').trim()||'#64748b';
Chart.defaults.color=text;Chart.defaults.font.family='Inter, sans-serif';
const dates=@json(collect($asistencias)->pluck('fecha')->map(fn($d)=>\Illuminate\Support\Carbon::parse($d)->format('d/m')));
new Chart(document.getElementById('attendanceChart'),{type:'line',data:{labels:dates,datasets:[{label:'Visitas',data:@json(collect($asistencias)->pluck('visitas')),borderColor:'#2563eb',backgroundColor:'rgba(37,99,235,.12)',fill:true,tension:.35},{label:'Clientes únicos',data:@json(collect($asistencias)->pluck('clientes')),borderColor:'#10b981',tension:.35}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom'}},scales:{x:{grid:{display:false}},y:{beginAtZero:true,grid:{color:grid},ticks:{precision:0}}}}});
new Chart(document.getElementById('revenueChart'),{type:'bar',data:{labels:@json(collect($ingresos)->pluck('fecha')->map(fn($d)=>\Illuminate\Support\Carbon::parse($d)->format('d/m'))),datasets:[{label:'Ingresos',data:@json(collect($ingresos)->pluck('ingresos')),backgroundColor:'rgba(16,185,129,.78)',borderRadius:6}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{x:{grid:{display:false}},y:{beginAtZero:true,grid:{color:grid}}}}});
new Chart(document.getElementById('membershipChart'),{type:'doughnut',data:{labels:@json(collect($membresiasPlanes)->pluck('plan')),datasets:[{data:@json(collect($membresiasPlanes)->pluck('cantidad')),backgroundColor:['#2563eb','#10b981','#f59e0b','#ef4444','#8b5cf6','#06b6d4'],borderWidth:0}]},options:{responsive:true,maintainAspectRatio:false,cutout:'68%',plugins:{legend:{position:'bottom',labels:{boxWidth:10,usePointStyle:true}}}}});
</script>
@endpush
