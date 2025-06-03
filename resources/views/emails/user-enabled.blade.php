<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>TicketGo - Notificación</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; background: #f4f6fb; margin: 0; padding: 0; }
        .container { background: #fff; max-width: 520px; margin: 40px auto; border-radius: 12px; box-shadow: 0 4px 24px #2563eb22; padding: 40px 32px 32px 32px; border-top: 6px solid #2563eb; }
        .header { text-align: center; color: #2563eb; font-size: 2em; font-weight: bold; margin-bottom: 32px; letter-spacing: 1px; }
        .button {
            display: inline-block;
            background: #2563eb;
            color: #fff !important;
            padding: 14px 38px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            margin: 32px 0 24px 0;
            font-size: 1.1em;
            box-shadow: 0 2px 8px #2563eb33;
            transition: background 0.2s, box-shadow 0.2s;
        }
        .button:hover { background: #1e40af; box-shadow: 0 4px 16px #2563eb44; }
        .signature {
            margin-top: 40px;
            color: #444;
            font-size: 1.08em;
        }
        .footer {
            margin-top: 40px;
            font-size: 0.93em;
            color: #aaa;
            text-align: center;
        }
        .main-title { font-size: 1.25em; font-weight: bold; color: #222; margin-bottom: 18px; }
        .main-text { font-size: 1.08em; color: #333; margin-bottom: 8px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">TicketGo</div>
        <div class="main-title">¡Hola!</div>
        <div class="main-text">
            Tu cuenta en <strong>TicketGo</strong> ha sido habilitada correctamente.<br>
            Ya puedes acceder al sistema con tus credenciales.
        </div>
        <a href="{{ url('/login') }}" class="button">Ir a la plataforma</a>
        <div class="signature">
            Gracias por usar TicketGo.<br>
            <strong>Equipo TicketGo</strong>
        </div>
        <div class="footer">
            Si tienes problemas con el botón, copia y pega este enlace en tu navegador:<br>
            <a href="{{ url('/login') }}">{{ url('/login') }}</a>
            <br><br>
            &copy; {{ date('Y') }} TicketGo. Todos los derechos reservados.
        </div>
    </div>
</body>
</html> 