<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>TicketGo - Notificación</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; background: #f6f8fb; margin: 0; padding: 0; }
        .container { background: #fff; max-width: 500px; margin: 30px auto; border-radius: 8px; box-shadow: 0 2px 8px #e0e0e0; padding: 32px 24px; }
        .header { text-align: center; color: #2d3748; font-size: 1.6em; font-weight: bold; margin-bottom: 24px; }
        .button {
            display: inline-block;
            background: #2563eb;
            color: #fff !important;
            padding: 12px 32px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            margin: 24px 0;
            transition: background 0.2s;
        }
        .button:hover { background: #1e40af; }
        .footer {
            margin-top: 32px;
            font-size: 0.9em;
            color: #888;
            text-align: center;
        }
        .signature {
            margin-top: 32px;
            color: #444;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            TicketGo
        </div>
        <p style="font-size: 1.1em;"><strong>¡Hola!</strong></p>
        <p>
            Se ha <strong>comentado un ticket</strong> en TicketGo.<br>
            {{-- Aquí puedes agregar detalles dinámicos del ticket si lo deseas --}}
        </p>
        <a href="{{ url('/tickets') }}" class="button">Ver comentario</a>
        <p class="signature">
            Gracias por usar TicketGo.<br>
            <strong>Equipo TicketGo</strong>
        </p>
        <div class="footer">
            Si tienes problemas con el botón, copia y pega este enlace en tu navegador:<br>
            <a href="{{ url('/tickets') }}">{{ url('/tickets') }}</a>
            <br><br>
            &copy; {{ date('Y') }} TicketGo. Todos los derechos reservados.
    </div>
    </div>
</body>
</html> 