@extends('emails.invites.layout')

@section('body')
<p style="margin: 0 0 20px; font-size: 16px;">Olá, {{ $name }}!</p>

<p style="margin: 0 0 24px; font-size: 15px; line-height: 1.6; color: #555555;">
    Você recebeu um convite de acesso <strong>operacional</strong> à plataforma.
    Clique no botão abaixo para continuar.
</p>

<table cellpadding="0" cellspacing="0">
    <tr>
        <td style="border-radius: 6px; background: #2563eb;">
            <a href="{{ $link }}"
               style="display: inline-block; padding: 12px 22px; color: #ffffff; text-decoration: none; font-size: 14px; font-weight: 600;">
                Acessar plataforma
            </a>
        </td>
    </tr>
</table>

<p style="margin: 28px 0 8px; font-size: 13px; color: #777777;">
    Caso o botão não funcione, utilize o link abaixo:
</p>

<a href="{{ $link }}"
   style="font-size: 13px; color: #2563eb; word-break: break-all;">
    {{ $link }}
</a>
@endsection
