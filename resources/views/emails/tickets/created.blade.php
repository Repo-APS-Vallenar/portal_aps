@php
    $color = [
        'baja' => '#198754',
        'media' => '#0d6efd',
        'alta' => '#dc3545',
        'urgente' => '#fd7e14',
    ][$ticket->priority] ?? '#0d6efd';
    $logoUrl = 'https://placehold.co/120x40/0d6efd/fff?text=APS+Portal'; // Cambia esta URL por tu logo real
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Ticket Creado</title>
</head>
<body style="margin:0; padding:0; background:#f6f8fa; font-family: Arial, Helvetica, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f6f8fa; padding: 0; margin: 0;">
        <tr>
            <td align="center">
                <table width="520" cellpadding="0" cellspacing="0" border="0" style="margin:32px 0;">
                    <tr>
                        <td style="padding:0;">
                            <!-- Encabezado con logo -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#0d6efd; border-radius:14px 14px 0 0;">
                                <tr>
                                    <td align="center" style="padding:18px 0 8px 0;">
                                        <img src="{{ $logoUrl }}" alt="Logo APS Portal" style="max-width:160px; max-height:48px; display:block; margin:0 auto 8px auto; border-radius:6px;">
                                        <div style="color:#fff; font-size:1.5em; font-weight:bold; margin-top:4px; letter-spacing:1px;">Nuevo ticket creado</div>
                                    </td>
                                </tr>
                            </table>
                            <!-- Card principal -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#fff; border-radius:0 0 14px 14px; box-shadow:0 2px 12px #0001;">
                                <tr>
                                    <td style="padding:32px 24px 16px 24px;">
                                        <div style="color:#0d6efd; font-size:1.15em; font-weight:600; margin-bottom:18px;">Ticket #{{ $ticket->id }}</div>
                                        <!-- Card de datos -->
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f8fafd; border-radius:10px; margin-bottom:18px;">
                                            <tr>
                                                <td style="padding:18px 18px 10px 18px;">
                                                    <div style="font-size:1.08em; margin-bottom:10px;"><span style="font-size:1.2em;">📝</span> <b>Título:</b> {{ $ticket->title }}</div>
                                                    <div style="font-size:1.08em; margin-bottom:10px;"><span style="font-size:1.2em;">📄</span> <b>Descripción:</b> {{ $ticket->description }}</div>
                                                    <div style="font-size:1.08em; margin-bottom:10px;"><span style="font-size:1.2em;">🏷️</span> <b>Categoría:</b> {{ $ticket->category->name ?? 'Sin categoría' }}</div>
                                                    <div style="font-size:1.08em; margin-bottom:10px;"><span style="font-size:1.2em;">👤</span> <b>Creado por:</b> {{ $ticket->creator->name }}</div>
                                                    <div style="font-size:1.08em; margin-bottom:10px;"><span style="font-size:1.2em;">⚡</span> <b>Prioridad:</b> <span style="color:{{ $color }}; font-weight:bold;">{{ ucfirst($ticket->priority) }}</span></div>
                                                </td>
                                            </tr>
                                        </table>
                                        <table cellpadding="0" cellspacing="0" border="0" align="center" style="margin: 24px 0 24px 0;">
                                            <tr>
                                                <td align="center">
                                                    <a href="{{ url('/tickets/' . $ticket->id) }}" style="display:inline-block; background:#0d6efd; color:#fff; padding:14px 36px; border-radius:8px; text-decoration:none; font-weight:700; font-size:1.12em; box-shadow:0 2px 8px #0d6efd33; letter-spacing:0.5px;">Ver ticket</a>
                                                </td>
                                            </tr>
                                        </table>
                                        <hr style="border:none; border-top:2px solid #e9ecef; margin:24px 0;">
                                        <div style="color:#6c757d; font-size:1em; text-align:center;">
                                            Este es un mensaje automático, por favor no responder.<br>
                                            Saludos,<br>
                                            El equipo de soporte
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html> 