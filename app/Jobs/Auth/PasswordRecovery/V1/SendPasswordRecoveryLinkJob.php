<?php

namespace App\Jobs\Auth\PasswordRecovery\V1;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendPasswordRecoveryLinkJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public string $email,
        public string $name,
        public string $resetUrl,
    ) {}

    public function handle(): void
    {
        Mail::raw($this->buildMessage(), function ($message): void {
            $message->to($this->email, $this->name)
                ->subject('Recuperação de senha');
        });
    }

    protected function buildMessage(): string
    {
        return sprintf(
            "Olá %s,\n\nRecebemos uma solicitação para redefinir sua senha.\n\nAcesse o link abaixo em até %s minutos:\n%s\n\nSe você não solicitou essa alteração, ignore este e-mail.",
            $this->name,
            10,
            $this->resetUrl
        );
    }
}
