<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Cuenta bloqueada</title>
</head>

<body>
    <h2>¡Hola {{ $user->name }}!</h2>
    <p>Tu cuenta ha sido bloqueada temporalmente debido a múltiples intentos fallidos de inicio de sesión.</p>
    <p>Podrás volver a intentarlo después de <strong>{{ optional($user->locked_until)->format('d/m/Y H:i') }}</strong>.
    </p>
    <p>Si no fuiste tú, por favor contacta al administrador del sistema.</p>
    <br>
    <small>Este es un mensaje automático del sistema.</small>
</body>

</html>