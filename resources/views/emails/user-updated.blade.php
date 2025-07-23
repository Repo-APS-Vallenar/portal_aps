<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuario actualizado</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; background:rgb(54, 168, 64); margin: 0; padding: 0; line-height: 1.6; color: #333; }
        .card {
            background: rgb(190, 212, 245);
            max-width: 550px;
            margin: 30px auto;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 32px 28px;
        }
        .header { text-align: center; color: #1a202c; font-size: 1.8em; font-weight: bold; margin-bottom: 28px; padding-bottom: 15px; border-bottom: 1px solid #eee; }
        .content p { margin-bottom: 1em; font-size: 1.05em; }
        .datacard {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }
        .datacard-title { font-weight: bold; color: #1a202c; margin-bottom: 10px; }
        .datacard-content { color: #4a5568; }
        .changes-list {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
        }
        .changes-list ul {
            margin: 0;
            padding-left: 20px;
        }
        .changes-list li {
            margin-bottom: 5px;
        }
        .button { display: inline-block; background: #2563eb; color: #fff !important; padding: 14px 40px; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 1.1em; transition: background 0.2s; margin: 20px 0; }
        .button:hover { background: #1e40af; }
        .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; color: #718096; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            Usuario actualizado
        </div>
        <div class="content">
            <p>¡La información del usuario <strong>{{ $user->name }}</strong> ha sido actualizada exitosamente!</p>
            
            <div class="datacard">
                <div class="datacard-title">Información actual del usuario:</div>
                <div class="datacard-content">
                    <p><strong>Nombre:</strong> {{ $user->name }}</p>
                    <p><strong>Email:</strong> {{ $user->email }}</p>
                    <p><strong>Rol:</strong> {{ ucfirst($user->role) }}</p>
                </div>
            </div>

            @if(!empty($changes))
            <div class="changes-list">
                <div class="datacard-title">Cambios realizados:</div>
                <ul>
                    @foreach($changes as $change)
                        <li>{{ $change }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            
            <div style="text-align:center;">
                <a href="{{ url('/admin/users') }}" class="button">Ver todos los usuarios</a>
            </div>
        </div>
        <div class="footer">
            <p>¡Saludos!<br><strong>APS | TicketGo</strong></p>
        </div>
    </div>
</body>
</html>
