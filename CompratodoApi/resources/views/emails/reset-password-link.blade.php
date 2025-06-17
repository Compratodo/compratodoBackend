<!DOCTYPE html>
<html>
<head>
    <title>Recuperar contraseña</title>
</head>
<body>
    <h2>Hola, {{ $userName }}</h2>
    <p>Recibimos una solicitud para restablecer tu contraseña.</p>
    <p>
        Haz clic en el siguiente botón para establecer una nueva contraseña:
    </p>
    <p style="margin-top: 20px;">
        <a href="{{ $url }}" style="background-color: #0d6efd; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Restablecer contraseña</a>
    </p>
    <p>Este enlace expirará en 30 minutos.</p>
</body>
</html>
