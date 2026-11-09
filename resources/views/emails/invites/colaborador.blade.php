@extends('emails.invites.layout')

@section('body')
<p style="margin: 0 0 20px; font-size: 16px;">Olá, {{ $name }}!</p>

<p style="margin: 0 0 24px; font-size: 15px; line-height: 1.6; color: #555555;">
    Você foi convidado(a) a integrar a plataforma com perfil de
    <strong>colaboração e gestão</strong>. Acesse para revisar planos, usuários
    e permissões da sua empresa.
</p>

<table cellpadding="0" cellspacing="0">
    <tr>
        <td style="border-radius: 6px; background: #1d4ed8;">
            <a href="{{ $link }}"
               style="display: inline-block; padding: 12px 22px; color: #ffffff; text-decoration: none; font-size: 14px; font-weight: 600;">
                Acessar painel de gestão
            </a>
        </td>
    </tr>
</table>

<p style="margin: 28px 0 8px; font-size: 13px; color: #777777;">
    Caso o botão não funcione, utilize o link abaixo:
</p>

<a href="{{ $link }}"
   style="font-size: 13px; color: #1d4ed8; word-break: break-all;">
    {{ $link }}
</a>
@endsection
