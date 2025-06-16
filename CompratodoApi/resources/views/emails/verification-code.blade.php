<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Notificación' }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f5f5f5;
        }
        .email-container {
            max-width: 700px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            padding: 40px 20px;
            text-align: center;
            color: white;
        }
        .logo {
            display: inline-flex;
            align-items: center;
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .logo-icon {
            width: 32px;
            height: 32px;
            background-color: #10b981;
            border-radius: 6px;
            margin-right: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }
        .greeting {
            font-size: 32px;
            font-weight: 300;
            margin: 0;
        }
        .content {
            padding: 40px;
            background-color: #ffffff;
        }
        .welcome-text {
            color: #6b7280;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .main-message {
            color: #374151;
            font-size: 18px;
            font-weight: 600;
            line-height: 1.5;
            margin-bottom: 30px;
        }
        .code-container {
            background-color: #f9fafb;
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 30px 0;
        }
        .code-label {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .verification-code {
            font-family: 'Courier New', monospace;
            font-size: 24px;
            font-weight: bold;
            color: #1f2937;
            letter-spacing: 3px;
            background-color: #ffffff;
            padding: 15px 25px;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
            display: inline-block;
        }
        .expiry-text {
            color: #6b7280;
            font-size: 14px;
            margin-top: 15px;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: white;
            text-decoration: none;
            padding: 15px 30px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 16px;
            margin: 20px 0;
            transition: all 0.3s ease;
        }
        .button:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        }
        .footer-message {
            color: #6b7280;
            font-size: 16px;
            line-height: 1.6;
            margin-top: 30px;
        }
        .signature {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
        .signature-text {
            color: #374151;
            font-size: 16px;
            margin-bottom: 5px;
        }
        .company-name {
            color: #1f2937;
            font-weight: 600;
        }
        .footer {
            background-color: #f9fafb;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer-links {
            margin-bottom: 20px;
        }
        .footer-link {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 500;
            margin: 0 15px;
        }
        .footer-link:hover {
            text-decoration: underline;
        }
        .help-text {
            color: #6b7280;
            font-size: 14px;
        }
        @media (max-width: 600px) {
            .email-container {
                margin: 0;
                border-radius: 0;
            }
            .header {
                padding: 30px 20px;
            }
            .content {
                padding: 30px 20px;
            }
            .greeting {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div class="logo">
                
                {{ config('app.name', 'SOAW') }}
            </div>
            <h1 class="greeting">¡Hola! {{ $userName ?? 'Usuario' }}</h1>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="welcome-text">
                {{ $welcomeMessage ?? 'Gracias por usar nuestros servicios. Nos complace tenerte con nosotros.' }}
            </div>

            <div class="main-message">
                {{ $mainMessage ?? 'Para completar el proceso, necesitamos que verifiques la siguiente información.' }}
            </div>

            @if(isset($verificationCode))
            <div class="code-container">
                <div class="code-label">Copia y pega este código para poder validar:</div>
                <div class="verification-code">{{ $verificationCode }}</div>
                <div class="expiry-text">
                    Este código es válido por {{ $expiryTime ?? '24 horas' }}. Si no hiciste esta solicitud, puedes ignorar este mensaje.
                </div>
            </div>
            @endif

            @if(isset($actionUrl) && isset($actionText))
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $actionUrl }}" class="button">{{ $actionText }}</a>
            </div>
            @endif

            @if(isset($additionalContent))
            <div class="footer-message">
                {!! $additionalContent !!}
            </div>
            @endif

            <div class="signature">
                <div class="signature-text">Gracias por confiar en nosotros.</div>
                <div class="company-name">El equipo de {{ config('app.name', 'SOAW') }}</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-links">
                <a href="{{ config('app.url') }}" class="footer-link">{{ config('app.url') ?? 'soaw.com' }}</a>
                @if(isset($contactUrl))
                <a href="{{ $contactUrl }}" class="footer-link">¿Necesitas ayuda? Contáctanos</a>
                @endif
            </div>
            <div class="help-text">
                Si tienes alguna pregunta, no dudes en contactarnos.
            </div>
        </div>
    </div>
</body>
</html>