<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<style>
    @page { margin: 34px 42px 52px; }
    * { box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; color:#24324a; font-size:10px; line-height:1.45; }
    .footer { position:fixed; bottom:-32px; left:0; right:0; border-top:1px solid #dbe3ef; padding-top:7px; color:#718096; font-size:8px; text-align:center; }
    .page:after { content:counter(page); }
    .header { width:100%; border-collapse:collapse; border-bottom:3px solid #0b2d5c; margin-bottom:18px; padding-bottom:12px; }
    .header td { vertical-align:middle; }
    .logo { max-width:185px; max-height:76px; }
    .fallback-logo { color:#0b2d5c; font-size:25px; font-weight:bold; letter-spacing:1px; }
    .fallback-logo small { display:block; font-size:8px; letter-spacing:2.2px; font-weight:normal; }
    .document-meta { text-align:right; }
    .document-meta strong { color:#0b2d5c; font-size:16px; }
    .document-meta span { display:block; color:#64748b; margin-top:3px; }
    .title { text-align:center; color:#0b2d5c; font-size:18px; margin:0 0 4px; text-transform:uppercase; letter-spacing:.6px; }
    .subtitle { text-align:center; color:#64748b; margin:0 0 16px; }
    .summary { width:100%; border-collapse:collapse; margin-bottom:17px; }
    .summary td { width:50%; padding:8px 10px; border:1px solid #dbe3ef; }
    .summary .label { display:block; color:#64748b; font-size:8px; text-transform:uppercase; letter-spacing:.5px; }
    .summary strong { color:#17243b; font-size:10.5px; }
    h2 { color:#0b2d5c; font-size:11.5px; margin:14px 0 5px; padding-bottom:4px; border-bottom:1px solid #dbe3ef; }
    p { margin:4px 0 8px; text-align:justify; }
    ol { margin:5px 0 8px 18px; padding:0; }
    li { margin-bottom:4px; text-align:justify; }
    .notice { margin:12px 0; padding:9px 11px; background:#eef5ff; border-left:3px solid #2563eb; color:#1e3a5f; }
    .beneficiaries { width:100%; border-collapse:collapse; margin:7px 0 12px; }
    .beneficiaries th { color:#fff; background:#0b2d5c; padding:6px; text-align:left; }
    .beneficiaries td { padding:6px; border-bottom:1px solid #dbe3ef; }
    .signatures { width:100%; margin-top:36px; border-collapse:collapse; page-break-inside:avoid; }
    .signatures td { width:48%; vertical-align:bottom; text-align:center; padding:0 15px; }
    .signature-line { border-top:1px solid #24324a; padding-top:6px; min-height:36px; }
    .legal { color:#718096; font-size:8px; margin-top:18px; text-align:center; }
</style>
</head>
<body>
@php
    $logoPath = public_path('images/image.svg');
    $activos = collect($beneficiarios)->where('estado', 'ACTIVO');
    $esGrupal = in_array($membresia->tipo_codigo ?? '', ['PAREJA', 'FAMILIAR'], true);
@endphp
<div class="footer">FitControl · {{ $contrato->numero_contrato }} · Documento versión {{ $contrato->version_documento }} · Página <span class="page"></span></div>

<table class="header"><tr><td>
    @if(file_exists($logoPath))
        <img class="logo" src="{{ $logoPath }}" alt="FitControl">
    @else
        <div class="fallback-logo">FITCONTROL<small>GYM MANAGEMENT SYSTEM</small></div>
    @endif
</td><td class="document-meta"><strong>CONTRATO DE MEMBRESÍA</strong><span>{{ $contrato->numero_contrato }}</span><span>Versión {{ $contrato->version_documento }}</span></td></tr></table>

<h1 class="title">Acuerdo de prestación de servicios</h1>
<p class="subtitle">Condiciones de afiliación, acceso y uso de las instalaciones de FitControl</p>

<table class="summary">
    <tr><td><span class="label">Titular</span><strong>{{ $contrato->cliente_nombre }}</strong></td><td><span class="label">Identificación</span><strong>{{ $contrato->cliente_identificacion ?: 'No registrada' }}</strong></td></tr>
    <tr><td><span class="label">Número de socio</span><strong>{{ $membresia->numero_socio }}</strong></td><td><span class="label">Plan contratado</span><strong>{{ $contrato->plan_nombre }}</strong></td></tr>
    <tr><td><span class="label">Precio total</span><strong>{{ number_format($contrato->precio,2) }} {{ $contrato->moneda }}</strong></td><td><span class="label">Estado actual</span><strong>{{ $membresia->estado }}</strong></td></tr>
    <tr><td><span class="label">Fecha de inicio</span><strong>{{ \Illuminate\Support\Carbon::parse($contrato->fecha_inicio)->format('d/m/Y') }}</strong></td><td><span class="label">Fecha de finalización</span><strong>{{ \Illuminate\Support\Carbon::parse($contrato->fecha_fin)->format('d/m/Y') }}</strong></td></tr>
</table>

<h2>1. Objeto del contrato</h2>
<p>FitControl concede al titular el derecho personal de acceso a las instalaciones y servicios incluidos en el plan contratado durante su periodo de vigencia, sujeto al reglamento interno, horarios, capacidad disponible y condiciones establecidas en este documento.</p>

<h2>2. Precio, pago y activación</h2>
<ol>
    <li>El precio total contratado es de <strong>{{ number_format($contrato->precio,2) }} {{ $contrato->moneda }}</strong> y debe pagarse en una sola exhibición.</li>
    <li>La creación del contrato no autoriza el acceso. La membresía se activa únicamente después de que el pago total quede registrado y aplicado.</li>
    <li>Los pagos, recibos y cambios de estado se conservarán como parte del historial de la membresía.</li>
</ol>

@if($esGrupal)
<h2>3. Titular y beneficiarios del plan {{ $membresia->tipo_codigo === 'PAREJA' ? 'Pareja' : 'Familiar' }}</h2>
<p>El titular es responsable del pago, del uso adecuado del plan y de comunicar estas condiciones a todos los beneficiarios. Cada beneficiario debe contar con expediente y número de socio individual.</p>
<table class="beneficiaries"><thead><tr><th>Número de socio</th><th>Beneficiario</th><th>Parentesco</th><th>Estado</th></tr></thead><tbody>
@forelse($activos as $beneficiario)<tr><td>{{ $beneficiario->numero_socio }}</td><td>{{ $beneficiario->cliente }}</td><td>{{ $beneficiario->parentesco }}</td><td>{{ $beneficiario->estado }}</td></tr>
@empty<tr><td colspan="4">No había beneficiarios activos al momento de emitir este documento.</td></tr>@endforelse
</tbody></table>
<div class="notice">Los cambios posteriores de beneficiarios quedarán registrados en el historial del grupo y funcionarán como anexo operativo de este contrato.</div>
@endif

<h2>{{ $esGrupal ? '4' : '3' }}. Acceso y uso</h2>
<ol>
    <li>El acceso es personal e intransferible y se valida con el expediente individual del cliente.</li>
    <li>No se permitirá el ingreso con membresía pendiente, vencida, cancelada, congelada o fuera de sus fechas de vigencia.</li>
    <li>El titular y los beneficiarios se obligan a cuidar equipos e instalaciones y seguir las instrucciones de seguridad del personal.</li>
</ol>

<h2>{{ $esGrupal ? '5' : '4' }}. Congelación</h2>
<p>{{ $contrato->politica_congelacion }} La congelación afecta el acceso del titular y de todos los beneficiarios asociados durante el periodo autorizado.</p>

<h2>{{ $esGrupal ? '6' : '5' }}. Cancelación y conservación del historial</h2>
<p>{{ $contrato->politica_cancelacion }} La cancelación exige motivo, categoría, fecha efectiva y usuario responsable. No elimina contratos, pagos, asistencias ni eventos previamente registrados.</p>

<h2>{{ $esGrupal ? '7' : '6' }}. Salud, responsabilidad y privacidad</h2>
<ol>
    <li>El cliente declara que la información suministrada es veraz y se compromete a informar condiciones que puedan limitar la práctica de ejercicio.</li>
    <li>FitControl tratará la información personal y médica con acceso restringido según los permisos del sistema.</li>
    <li>El cliente debe suspender la actividad y solicitar asistencia ante dolor, mareo o cualquier síntoma adverso.</li>
</ol>

<h2>{{ $esGrupal ? '8' : '7' }}. Aceptación</h2>
<p>{{ $contrato->condiciones }} La aceptación electrónica registrada tiene como evidencia la fecha, usuario responsable, versión del documento y dirección IP disponible en el sistema.</p>

<table class="signatures"><tr><td><div class="signature-line"><strong>{{ $contrato->firma_nombre }}</strong><br>Titular de la membresía<br>{{ \Illuminate\Support\Carbon::parse($contrato->aceptado_at)->format('d/m/Y H:i') }}</div></td><td><div class="signature-line"><strong>{{ $contrato->registrado_por_nombre ?: 'FitControl' }}</strong><br>Representante / usuario responsable<br>Contrato aceptado electrónicamente</div></td></tr></table>

<p class="legal">Documento generado por FitControl. Verifique su autenticidad mediante el número único {{ $contrato->numero_contrato }}.</p>
</body>
</html>
