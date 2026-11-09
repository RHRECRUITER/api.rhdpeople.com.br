<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Convite - {{ config('app.name', 'Plataforma') }}</title>
</head>
<body style="margin: 0; padding: 0; background: #f5f6f8; font-family: Arial, Helvetica, sans-serif; color: #24292f;">
    <table width="100%" cellpadding="0" cellspacing="0" style="padding: 40px 20px;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0"
                       style="max-width: 520px; background: #ffffff; border-radius: 10px; overflow: hidden;">
                    <tr>
                        <td style="padding: 28px 32px; border-bottom: 1px solid #eeeeee;">
                            <strong style="font-size: 20px; color: #1f2937;">
                                {{ config('app.name', 'Plataforma') }}
                            </strong>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 32px;">
                            @yield('body')
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 20px 32px; background: #fafafa;">
                            <p style="margin: 0; font-size: 12px; color: #999999;">
                                Este e-mail foi enviado automaticamente. Por favor, não responda.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
