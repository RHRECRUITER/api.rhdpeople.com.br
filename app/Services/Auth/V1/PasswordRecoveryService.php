<?php

namespace App\Services\Auth\V1;

use App\Jobs\Auth\PasswordRecovery\V1\SendPasswordRecoveryConfirmationJob;
use App\Jobs\Auth\PasswordRecovery\V1\SendPasswordRecoveryLinkJob;
use App\Repositories\Contracts\PasswordRecoveryRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PasswordRecoveryService
{
    public function __construct(
        protected UserRepositoryInterface $users,
        protected PasswordRecoveryRepositoryInterface $tokens,
    ) {}

    public function requestRecovery(string $email): void
    {
        $user = $this->users->findByEmail($email);

        // Não confirmamos se o e-mail existe para evitar user enumeration.
        if (! $user) {
            return;
        }

        $token = (string) Str::uuid7();
        // A janela curta reduz abuso e obriga a geração de um novo link quando expira.
        $expiresAt = now()->addMinutes(10);
        $recoveryToken = $this->tokens->createToken($user->id, $token, $expiresAt);
        $resetUrl = $this->buildResetUrl($recoveryToken->token);

        SendPasswordRecoveryLinkJob::dispatch(
            $user->email,
            $user->name,
            $resetUrl,
        )->onConnection('redis')->onQueue('emails');
    }

    public function resetPassword(string $tokenValue, string $password): void
    {
        // A troca da senha e o consumo do token precisam acontecer de forma atômica.
        DB::transaction(function () use ($tokenValue, $password): void {
            $token = $this->tokens->findByToken($tokenValue);

            if (! $token || $token->used_at !== null || $token->expires_at === null || $token->expires_at->isPast()) {
                throw ValidationException::withMessages([
                    'token' => ['Token inválido, expirado ou já utilizado.'],
                ]);
            }

            $token->user->update([
                'password' => Hash::make($password),
            ]);

            $this->tokens->markAsUsed($token);

            // O aviso de sucesso só deve sair depois do commit para não notificar um reset revertido.
            SendPasswordRecoveryConfirmationJob::dispatch(
                $token->user->email,
                $token->user->name,
            )->onConnection('redis')->onQueue('emails')->afterCommit();
        });
    }

    protected function buildResetUrl(string $token): string
    {
        // O link precisa apontar para a SPA, não para a API.
        $frontendUrl = env('FRONTEND_URL', config('app.url'));

        return rtrim((string) $frontendUrl, '/').'/reset-password?token='.$token;
    }
}
