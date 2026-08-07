@extends('layouts.app')

@section('title', 'Detalle de auditoría')

@section('content')
@php
    $etiquetas = [
        'guard' => 'Método de acceso',
        'email' => 'Correo electrónico',
        'correo_electronico' => 'Correo electrónico',
        'nombre' => 'Nombre',
        'estado' => 'Estado',
        'rol' => 'Rol',
        'role' => 'Rol',
        'usuario_id' => 'Usuario',
        'cliente_id' => 'Cliente',
        'personal_id' => 'Empleado',
        'fecha' => 'Fecha',
        'observacion' => 'Observación',
    ];

    $aplanar = function (mixed $datos, string $prefijo = '') use (&$aplanar): array {
        if (! is_array($datos)) {
            return $prefijo !== '' ? [$prefijo => $datos] : [];
        }

        $resultado = [];
        foreach ($datos as $clave => $valor) {
            $ruta = $prefijo === '' ? (string) $clave : $prefijo.'.'.$clave;
            if (is_array($valor)) {
                $resultado += $aplanar($valor, $ruta);
            } else {
                $resultado[$ruta] = $valor;
            }
        }

        return $resultado;
    };

    $decodificar = static function (?string $valor): array {
        if (blank($valor)) {
            return [];
        }

        $datos = json_decode($valor, true);
        return is_array($datos) ? $datos : [];
    };

    $datosAnteriores = $decodificar($auditoria->valores_anteriores ?? null);
    $datosNuevos = $decodificar($auditoria->valores_nuevos ?? null);
    $procedimiento = $datosNuevos['procedimiento'] ?? null;
    $parametrosProcedimiento = is_array($datosNuevos['parametros'] ?? null) ? $datosNuevos['parametros'] : [];

    $definicionesProcedimientos = [
        'sp_asistencias_registrar_entrada' => [
            'accion' => 'Registrar entrada',
            'modulo' => 'Asistencias',
            'parametros' => ['Cliente', 'Membresía', 'Fecha y hora', 'Método', 'Registrado por', 'Observación'],
        ],
        'sp_asistencias_registrar_salida' => [
            'accion' => 'Registrar salida',
            'modulo' => 'Asistencias',
            'parametros' => ['Asistencia', 'Fecha y hora de salida', 'Registrado por'],
        ],
    ];

    $detalleProcedimiento = $procedimiento ? ($definicionesProcedimientos[$procedimiento] ?? null) : null;
    if ($procedimiento) {
        $partes = str($procedimiento)->after('sp_')->explode('_');
        $accionGenerica = str($auditoria->evento)->replace('_', ' ')->headline()->toString();
        $moduloGenerico = $partes->first() ? str($partes->first())->headline()->toString() : 'Sistema';
        $resumenOperacion = [
            'accion' => $detalleProcedimiento['accion'] ?? $accionGenerica,
            'modulo' => $detalleProcedimiento['modulo'] ?? $moduloGenerico,
        ];

        $nuevos = [];
        foreach ($parametrosProcedimiento as $indice => $valor) {
            if ($valor === null || $valor === '') {
                continue;
            }
            $nombre = $detalleProcedimiento['parametros'][$indice] ?? null;
            if ($nombre !== null && ! in_array($nombre, ['Registrado por'], true)) {
                $nuevos[$nombre] = $valor;
            }
        }
        $anteriores = [];
    } else {
        $resumenOperacion = null;
        $anteriores = $aplanar($datosAnteriores);
        $nuevos = $aplanar($datosNuevos);
    }

    $etiqueta = static function (string $clave) use ($etiquetas): string {
        if (str($clave)->contains(' ')) {
            return $clave;
        }
        $ultimoSegmento = str($clave)->afterLast('.')->toString();
        return $etiquetas[$ultimoSegmento] ?? str($ultimoSegmento)->replace('_', ' ')->headline()->toString();
    };

    $mostrarValor = static function (mixed $valor, string $clave): string {
        if (str($clave)->lower()->contains(['password', 'contraseña', 'token'])) {
            return 'Dato protegido';
        }
        if (is_bool($valor)) {
            return $valor ? 'Sí' : 'No';
        }
        if ($valor === null || $valor === '') {
            return '—';
        }
        if ($clave === 'guard' && $valor === 'web') {
            return 'Formulario web';
        }
        if (in_array($clave, ['Asistencia', 'Cliente', 'Membresía', 'Empleado'], true) && is_numeric($valor)) {
            return '#'.$valor;
        }

        return (string) $valor;
    };

    $nombreEvento = str($auditoria->evento)->replace('_', ' ')->headline();
    $nombreEntidad = str($auditoria->entidad)->replace('_', ' ')->headline();
@endphp

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1">Evento #{{ $auditoria->id }}</h1>
        <p class="text-muted mb-0">{{ $nombreEvento }} · {{ $nombreEntidad }}</p>
    </div>
    <a href="{{ route('auditoria.index') }}" class="btn btn-outline-secondary">Volver</a>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <dl class="row mb-0 gy-2">
                    <dt class="col-5">Fecha</dt>
                    <dd class="col-7">{{ \Illuminate\Support\Carbon::parse($auditoria->ocurrido_at)->format('d/m/Y H:i:s') }}</dd>
                    <dt class="col-5">Usuario</dt>
                    <dd class="col-7">{{ $auditoria->usuario ?: 'Sistema' }}</dd>
                    <dt class="col-5">Entidad / ID</dt>
                    <dd class="col-7">{{ $nombreEntidad }} / {{ $auditoria->entidad_id ?? '—' }}</dd>
                    <dt class="col-5">Dirección IP</dt>
                    <dd class="col-7">{{ $auditoria->ip ?? '—' }}</dd>
                    <dt class="col-5">Motivo</dt>
                    <dd class="col-7">{{ $auditoria->motivo ?? '—' }}</dd>
                    <dt class="col-5">Navegador</dt>
                    <dd class="col-7 text-break small">{{ $auditoria->user_agent ?? '—' }}</dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header fw-semibold">Información del evento</div>
            <div class="card-body {{ ($anteriores || $nuevos) ? 'pb-0' : '' }}">
                @if($resumenOperacion)
                    <div class="d-flex align-items-start gap-3">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary-subtle text-primary flex-shrink-0" style="width: 44px; height: 44px">
                            <i class="bi bi-check2-circle fs-4"></i>
                        </span>
                        <div>
                            <div class="text-muted small text-uppercase fw-semibold">Operación realizada</div>
                            <h2 class="h5 mb-1">{{ $resumenOperacion['accion'] }}</h2>
                            <p class="text-muted mb-0">Módulo {{ $resumenOperacion['modulo'] }} · Operación completada correctamente</p>
                        </div>
                    </div>
                @elseif(! $anteriores && ! $nuevos)
                    <p class="text-muted mb-0">Este evento no contiene datos adicionales.</p>
                @endif
            </div>
            @if($anteriores || $nuevos)
            <div class="card-body p-0 {{ $resumenOperacion ? 'mt-3' : '' }}">
                @if($anteriores || $nuevos)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Dato</th>
                                    @if($anteriores)<th>Valor anterior</th>@endif
                                    <th>{{ $anteriores ? 'Valor nuevo' : 'Valor' }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(array_unique(array_merge(array_keys($anteriores), array_keys($nuevos))) as $clave)
                                    <tr>
                                        <th class="fw-semibold">{{ $etiqueta($clave) }}</th>
                                        @if($anteriores)
                                            <td>{{ $mostrarValor($anteriores[$clave] ?? null, $clave) }}</td>
                                        @endif
                                        <td>{{ $mostrarValor($nuevos[$clave] ?? null, $clave) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
