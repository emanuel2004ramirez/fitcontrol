<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bienvenido a FitControl</title>
</head>
<body style="margin:0;background:#f4f7fb;font-family:Arial,sans-serif;color:#243047">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="padding:32px 16px;background:#f4f7fb">
        <tr><td align="center">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:620px;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 8px 30px rgba(36,48,71,.08)">
                <tr><td style="padding:30px;background:#176b5b;color:#ffffff">
                    <div style="font-size:25px;font-weight:700">FitControl</div>
                    <div style="margin-top:6px;opacity:.9">Tu experiencia en el gimnasio comienza aquí</div>
                </td></tr>
                <tr><td style="padding:32px">
                    <h1 style="margin:0 0 18px;font-size:25px;color:#16243a">¡Bienvenido, {{ $cliente->nombre }}!</h1>
                    <p style="line-height:1.7;margin:0 0 18px">Tu registro como cliente fue completado correctamente. Nos alegra acompañarte en el cumplimiento de tus objetivos.</p>
                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:24px 0;background:#f1f7f5;border-radius:12px">
                        <tr><td style="padding:20px">
                            <div style="font-size:13px;color:#657386">Número de socio</div>
                            <div style="margin-top:5px;font-size:22px;font-weight:700;color:#176b5b">{{ $cliente->numero_socio }}</div>
                        </td></tr>
                    </table>
                    <p style="line-height:1.7;margin:0">Conserva tu número de socio. El personal de recepción podrá ayudarte con la activación y pago de tu membresía.</p>
                </td></tr>
                <tr><td style="padding:20px 32px;background:#f8fafc;color:#778397;font-size:12px;text-align:center">Este correo fue generado automáticamente por FitControl.</td></tr>
            </table>
        </td></tr>
    </table>
</body>
</html>
