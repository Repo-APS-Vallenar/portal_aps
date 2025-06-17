<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>APS | TicketGo - Notificación</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; background: #f6f8fb; margin: 0; padding: 0; line-height: 1.6; color: #333; }
        .container { background: #fff; max-width: 550px; margin: 30px auto; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); padding: 32px 28px; }
        .header { text-align: center; color: #1a202c; font-size: 1.8em; font-weight: bold; margin-bottom: 28px; padding-bottom: 15px; border-bottom: 1px solid #eee; }
        .content p { margin-bottom: 1em; font-size: 1.05em; }
        .button-container { text-align: center; margin: 30px 0; }
        .button {
            display: inline-block;
            background: #2563eb;
            color: #fff !important;
            padding: 14px 40px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            font-size: 1.1em;
            transition: background 0.2s;
        }
        .button:hover { background: #1e40af; }
        .footer-text { margin-top: 35px; font-size: 0.85em; color: #777; text-align: center; line-height: 1.5; }
        .footer-link { color: #2563eb; text-decoration: none; }
        .signature { margin-top: 30px; color: #444; font-size: 1.05em; }
        .saludo { font-size: 1.2em; font-weight: bold; color: #333; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            APS | TicketGo
        </div>
        <div class="content">
            <p class="saludo">¡Hola!</p>
            <p>
                Se ha <strong>creado un nuevo usuario</strong> en TicketGo.<br>
                Ya puedes acceder al sistema con tus credenciales.
            </p>
            <div class="button-container">
                <a href="{{ url('/login') }}" class="button">Ir a la plataforma</a>
            </div>
            <p class="signature">
                Gracias por usar TicketGo.<br>
                <strong>Equipo TI APS</strong>
            </p>
        </div>
        <div class="footer-text">
            Si tienes problemas para hacer clic en el botón "Ir a la plataforma", copia y pega el siguiente enlace en tu navegador:<br>
            <a href="{{ url('/login') }}" class="footer-link">{{ url('/login') }}</a>
            <br><br>
            &copy; {{ date('Y') }} APS | TicketGo. Todos los derechos reservados.
        </div>
    </div>
</body>
</html> 