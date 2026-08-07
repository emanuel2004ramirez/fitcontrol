<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte - {{ $definition['title'] }}</title>
    <style>
        @page { margin: 40px 40px 50px; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #334155; }
        .header-table { width: 100%; border-bottom: 2px solid #2563eb; padding-bottom: 12px; margin-bottom: 20px; }
        .header-table td { vertical-align: middle; border: none; padding: 0; }
        .brand-name { font-size: 24px; font-weight: bold; color: #0f172a; margin: 0; letter-spacing: -.5px; }
        .report-title { font-size: 12px; color: #2563eb; font-weight: bold; text-transform: uppercase; margin-top: 4px; letter-spacing: .5px; }
        .meta-info { text-align: right; font-size: 9px; color: #64748b; line-height: 1.5; }
        .meta-info strong { color: #1e293b; }
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .data-table th, .data-table td { padding: 8px 6px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        .data-table th { background-color: #f8fafc; color: #0f172a; font-size: 10px; font-weight: bold; text-transform: uppercase; border-top: 1px solid #cbd5e1; border-bottom: 2px solid #cbd5e1; }
        .data-table tr:nth-child(even) td { background-color: #f8fafc; }
        .data-table td { font-size: 9.5px; color: #475569; }
        .footer { position: fixed; bottom: -30px; left: 0; right: 0; height: 20px; border-top: 1px solid #e2e8f0; padding-top: 8px; text-align: center; font-size: 9px; color: #94a3b8; }
        .page-number:before { content: "Página " counter(page) " de " counter(pages); }
    </style>
</head>
<body>
    @php
        $formatValue = static function (mixed $value, ?string $format): string {
            if ($value === null || $value === '') return '—';
            return match ($format) {
                'date' => \Illuminate\Support\Carbon::parse($value)->format('d/m/Y'),
                'datetime' => \Illuminate\Support\Carbon::parse($value)->format('d/m/Y H:i'),
                'money' => number_format((float) $value, 2),
                'decimal' => rtrim(rtrim(number_format((float) $value, 4, '.', ''), '0'), '.'),
                'minutes' => ((int) $value).' min',
                default => (string) $value,
            };
        };
    @endphp
    <div class="footer">FitControl - Sistema de Gestión · <span class="page-number"></span></div>

    <table class="header-table">
        <tr>
            <td width="50%">
                <h1 class="brand-name">FitControl</h1>
                <div class="report-title">{{ $definition['title'] }}</div>
            </td>
            <td width="50%" class="meta-info">
                <strong>Fecha de emisión:</strong> {{ now()->format('d/m/Y h:i A') }}<br>
                <strong>Total registros:</strong> {{ count($resultados) }}<br>
                <strong>Generado por:</strong> {{ auth()->check() ? auth()->user()->name : 'Sistema' }}
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead><tr>@foreach($definition['columns'] as $label)<th>{{ $label }}</th>@endforeach</tr></thead>
        <tbody>
            @forelse($resultados as $row)
                <tr>@foreach($definition['columns'] as $key => $label)<td>{{ $formatValue($row->{$key} ?? null, $definition['formats'][$key] ?? null) }}</td>@endforeach</tr>
            @empty
                <tr><td colspan="{{ count($definition['columns']) }}" style="text-align:center;padding:25px;color:#94a3b8;font-style:italic;">No hay datos disponibles para mostrar en este reporte.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
