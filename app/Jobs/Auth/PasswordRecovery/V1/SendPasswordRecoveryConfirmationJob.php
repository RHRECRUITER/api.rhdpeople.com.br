<?php

namespace App\Jobs\Auth\PasswordRecovery\V1;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendPasswordRecoveryConfirmationJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public string $email,
        public string $name,
    ) {}

    public function handle(): void
    {
        Mail::raw($this->buildMessage(), function ($message): void {
            $message->to($this->email, $this->name)
                ->subject('Senha alterada com sucesso');
        });
    }

    protected function buildMessage(): string
    {
        return sprintf(
            "Olá %s,\n\nSua senha foi alterada com sucesso. Se você não reconhece essa alteração, entre em contato com o suporte imediatamente.",
            $this->name
        );
    }
}
