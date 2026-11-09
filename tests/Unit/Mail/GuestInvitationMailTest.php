<?php

namespace Tests\Unit\Mail;

use App\Enums\TypeLicense;
use App\Mail\GuestInvitationMail;
use PHPUnit\Framework\TestCase;

class GuestInvitationMailTest extends TestCase
{
    /**
     * @dataProvider licenseProvider
     */
    public function test_seleciona_view_conforme_tipo_licenca(TypeLicense $type, string $expectedView): void
    {
        $mailable = new GuestInvitationMail(
            'João da Silva',
            'https://api.rhdpeople.com.br/invite/createrInviteRegister?token=abc123',
            $type,
        );
        $mailable->build();

        $this->assertSame($expectedView, $mailable->view);
        $this->assertSame('Convite para acessar a plataforma', $mailable->subject);
        $this->assertSame('João da Silva', $mailable->recipientName);
    }

    public static function licenseProvider(): array
    {
        return [
            'operacional' => [TypeLicense::OPERACIONAL, 'emails.invites.operacional'],
            'colaborador' => [TypeLicense::COLABORADOR, 'emails.invites.colaborador'],
            'dono usa fallback de gestao' => [TypeLicense::DONO, 'emails.invites.colaborador'],
        ];
    }
}
