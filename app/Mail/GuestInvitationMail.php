<?php

namespace App\Mail;

use App\Enums\TypeLicense;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;

/**
 * E-mail de convite de pré-cadastro de convidado.
 *
 * Enviado na fila (ShouldQueue) e renderiza uma view Blade diferente
 * conforme o tipo de licença do convidado (operacional vs. colaborador/gestão).
 */
class GuestInvitationMail extends Mailable implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $recipientName,
        public string $signedLink,
        public TypeLicense $typeLicense,
    ) {}

    public function build(): self
    {
        return $this
            ->subject('Convite para acessar a plataforma')
            ->view($this->templateView())
            ->with([
                'name' => $this->recipientName,
                'link' => $this->signedLink,
                'type' => $this->typeLicense->value,
            ]);
    }

    protected function templateView(): string
    {
        return match ($this->typeLicense) {
            TypeLicense::OPERACIONAL => 'emails.invites.operacional',
            default => 'emails.invites.colaborador',
        };
    }
}
