<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

class AuthenticationService
{
    /**
     * Autentica o usuário aplicando proteção contra força bruta por e-mail.
     *
     * @param  array{email: string, password: string}  $credentials
     *
     * @throws ValidationException Credenciais inválidas (422).
     * @throws TooManyRequestsHttpException Limite de tentativas excedido (429).
     */
    public function authenticate(array $credentials): void
    {
        $maxAttempts = (int) config('auth.rate_limit.max_attempts');
        $decaySeconds = (int) config('auth.rate_limit.decay_seconds');

        $email = Str::lower(trim($credentials['email']));
        $rateKey = 'login:'.$email;

        // 1. Verifica se a conta já está temporariamente bloqueada
        if ($email !== '' && RateLimiter::tooManyAttempts($rateKey, $maxAttempts)) {
            $retryAfter = RateLimiter::availableIn($rateKey);

            throw new TooManyRequestsHttpException(
                $retryAfter,
                'Muitas tentativas de login incorretas. Tente novamente em alguns minutos.'
            );
        }

        // 2. Tenta autenticar as credenciais informadas
        if (! Auth::attempt($credentials)) {
            // 3. Registra falha na chave do e-mail (apenas em erros)
            if ($email !== '') {
                RateLimiter::hit($rateKey, $decaySeconds);
            }

            throw ValidationException::withMessages([
                'email' => ['As credenciais estão incorretas.'],
            ]);
        }

        // 4. Sucesso: zera o contador de falhas acumuladas
        if ($email !== '') {
            RateLimiter::clear($rateKey);
        }
    }

    public function buildLoginResponse(User $user): array
    {
        // Load dos relacionamentos de licenças para evitar queries desnecessarias
        $user->load('licenses.plan.tenant');
        // Extração de helpers disponibilizados no model users
        $isSuperAdmin = $user->isSuperAdmin();
        $isCompanyAdmin = $user->isCompanyAdmin();
        $hasActiveLicense = $user->licenses()->where('is_active', true)->exists();

        // Deinição de role
        $primaryRole = 'user';
        if ($isSuperAdmin) {
            $primaryRole = 'super_admin';
        } elseif ($isCompanyAdmin) {
            $primaryRole = 'company_admin';
        } elseif ($hasActiveLicense) {
            $primaryRole = 'company_user';
        }
        // Regra de renovação de licensa
        $needRenewal = (! $isSuperAdmin && ! $hasActiveLicense);
        // Licensa com status atual
        $currentLicense = $user->licenses()->with('plan.tenant')->where('is_active', true)->first();
        $companyName = null;
        if ($currentLicense && $currentLicense->plan && $currentLicense->plan->tenant) {
            $companyName = $currentLicense->plan->tenant->name;
        }

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'access' => [
                'role' => $primaryRole,
                'has_active_license' => $hasActiveLicense,
                'need_renewal' => $needRenewal,
            ],
            'context' => $currentLicense ? [
                'license_id' => $currentLicense->id,
                'type_license' => $currentLicense->type_license,
                'company' => $companyName,
            ] : null,
        ];
    }
}
