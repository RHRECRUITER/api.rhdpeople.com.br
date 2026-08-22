<?php

namespace App\Services\Guest;

class TemplateService
{
    public function getInvitationTemplate(string $name, $link): string
    {
        return "
            <!DOCTYPE html>
            <html lang='pt-BR'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>Convite</title>
            </head>

            <body style='margin: 0; padding: 0; background: #f5f6f8; font-family: Arial, Helvetica, sans-serif; color: #24292f;'>

                <table width='100%' cellpadding='0' cellspacing='0' style='padding: 40px 20px;'>
                    <tr>
                        <td align='center'>

                            <table width='100%' cellpadding='0' cellspacing='0'
                                   style='max-width: 520px; background: #ffffff; border-radius: 10px; overflow: hidden;'>

                                <tr>
                                    <td style='padding: 28px 32px; border-bottom: 1px solid #eeeeee;'>
                                        <strong style='font-size: 20px; color: #1f2937;'>
                                            Plataforma
                                        </strong>
                                    </td>
                                </tr>

                                <tr>
                                    <td style='padding: 32px;'>

                                        <p style='margin: 0 0 20px; font-size: 16px;'>
                                            Olá, {$name}!
                                        </p>

                                        <p style='margin: 0 0 24px; font-size: 15px; line-height: 1.6; color: #555555;'>
                                            Você recebeu um convite para acessar a plataforma.
                                            Clique no botão abaixo para continuar.
                                        </p>

                                        <table cellpadding='0' cellspacing='0'>
                                            <tr>
                                                <td style='border-radius: 6px; background: #2563eb;'>
                                                    <a href='{$link}'
                                                       style='display: inline-block; padding: 12px 22px; color: #ffffff; text-decoration: none; font-size: 14px; font-weight: 600;'>
                                                        Acessar plataforma
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>

                                        <p style='margin: 28px 0 8px; font-size: 13px; color: #777777;'>
                                            Caso o botão não funcione, utilize o link abaixo:
                                        </p>

                                        <a href='{$link}'
                                           style='font-size: 13px; color: #2563eb; word-break: break-all;'>
                                            {$link}
                                        </a>

                                    </td>
                                </tr>

                                <tr>
                                    <td style='padding: 20px 32px; background: #fafafa;'>
                                        <p style='margin: 0; font-size: 12px; color: #999999;'>
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
        ";;
    }
}
