<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Código de Verificación</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f0f2f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f0f2f5; padding: 40px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08);">

                    {{-- Header --}}
                    <tr>
                        <td style="background: linear-gradient(135deg, #1a365d 0%, #2d4a7a 50%, #1a365d 100%); padding: 40px 40px 30px; text-align: center;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="center">
                                        <div style="width: 70px; height: 70px; background: linear-gradient(135deg, #F5C518, #d4a017); border-radius: 50%; margin: 0 auto 20px; display: inline-block; line-height: 70px; text-align: center;">
                                            <span style="font-size: 32px; color: #1a365d;">&#128274;</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <h1 style="color: #F5C518; font-size: 26px; font-weight: 700; margin: 0 0 8px; letter-spacing: 0.5px;">PresupuestoBO</h1>
                                        <p style="color: rgba(255,255,255,0.7); font-size: 13px; margin: 0; letter-spacing: 1px; text-transform: uppercase;">Agencia Boliviana de Correos</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Línea dorada --}}
                    <tr>
                        <td style="background: linear-gradient(90deg, #F5C518, #d4a017, #F5C518); height: 4px; font-size: 0; line-height: 0;">&nbsp;</td>
                    </tr>

                    {{-- Contenido --}}
                    <tr>
                        <td style="padding: 40px 44px 20px;">
                            <h2 style="color: #1a365d; font-size: 22px; font-weight: 700; margin: 0 0 8px;">Hola, {{ $nombre }}</h2>
                            <p style="color: #64748b; font-size: 15px; line-height: 1.7; margin: 0 0 28px;">
                                Hemos recibido una solicitud para restablecer la contraseña de tu cuenta. Utiliza el siguiente código de verificación para continuar:
                            </p>

                            {{-- Código de verificación --}}
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-bottom: 28px;">
                                <tr>
                                    <td align="center" style="padding: 24px 0;">
                                        <table role="presentation" cellspacing="0" cellpadding="0">
                                            <tr>
                                                @foreach(str_split($code) as $digit)
                                                <td style="padding: 0 5px;">
                                                    <div style="width: 52px; height: 64px; background: linear-gradient(135deg, #1a365d, #2d4a7a); border-radius: 12px; text-align: center; line-height: 64px; font-size: 28px; font-weight: 800; color: #F5C518; letter-spacing: 2px; box-shadow: 0 4px 12px rgba(26,54,93,0.3);">
                                                        {{ $digit }}
                                                    </div>
                                                </td>
                                                @endforeach
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            {{-- Aviso de expiración --}}
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-bottom: 28px;">
                                <tr>
                                    <td style="background-color: #fefce8; border-left: 4px solid #F5C518; border-radius: 0 8px 8px 0; padding: 16px 20px;">
                                        <p style="color: #854d0e; font-size: 14px; margin: 0; line-height: 1.6;">
                                            <strong>&#9888; Importante:</strong> Este código expirará en <strong>{{ $expireMinutes }} minutos</strong>. No compartas este código con nadie.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            {{-- Nota de seguridad --}}
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-bottom: 8px;">
                                <tr>
                                    <td style="background-color: #f8fafc; border-radius: 8px; padding: 16px 20px;">
                                        <p style="color: #94a3b8; font-size: 13px; margin: 0; line-height: 1.6;">
                                            Si no solicitaste restablecer tu contraseña, puedes ignorar este correo de forma segura. Tu cuenta permanecerá protegida.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Separador --}}
                    <tr>
                        <td style="padding: 0 44px;">
                            <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 0;">
                        </td>
                    </tr>

                    {{-- Seguridad --}}
                    <tr>
                        <td style="padding: 24px 44px 16px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td width="36" valign="top">
                                        <span style="font-size: 20px;">&#128737;</span>
                                    </td>
                                    <td style="padding-left: 12px;">
                                        <p style="color: #64748b; font-size: 13px; margin: 0; line-height: 1.6;">
                                            <strong style="color: #475569;">Consejo de seguridad:</strong> Nunca compartas tu contraseña ni este código con nadie. Nuestro equipo nunca te pedirá esta información.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background-color: #1a365d; padding: 28px 44px; text-align: center;">
                            <p style="color: #F5C518; font-size: 15px; font-weight: 700; margin: 0 0 6px; letter-spacing: 0.5px;">PresupuestoBO</p>
                            <p style="color: rgba(255,255,255,0.5); font-size: 12px; margin: 0 0 12px;">Sistema de Gestión Presupuestaria</p>
                            <p style="color: rgba(255,255,255,0.35); font-size: 11px; margin: 0;">
                                &copy; {{ date('Y') }} Agencia Boliviana de Correos. Todos los derechos reservados.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
