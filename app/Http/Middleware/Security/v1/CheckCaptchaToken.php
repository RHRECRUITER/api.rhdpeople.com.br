<?php

namespace App\Http\Middleware\Security\v1;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;

/**
 * Middleware responsável por validar tokens de contexto gerados no captcha/verify.
 *
 * Regras:
 * - Token pode vir por header `X-Validation-Token` ou body `validation_token`
 * - Token é single-use (consumido após validação bem-sucedida)
 * - Contexto deve bater com o exigido na rota (ex.: leads_landing)
 * - Vínculo de proprietário:
 *   - Preferencialmente pela sessão (quando existir/persistir)
 *   - Fallback por fingerprint (IP + User-Agent) para cenários stateless em /api
 */
class CheckCaptchaToken
{
    public function handle(Request $request, Closure $next, string $requiredContext)
    {
        $token = $request->header('X-Validation-Token') ?? $request->input('validation_token');

        if (! $token) {
            return Response::api(null, 'Acesso não autorizado', 403);
        }

        $cacheKey = "validated_action:{$token}";
        $storedData = Cache::get($cacheKey);

        if (! $storedData) {
            Log::warning("Captcha Debug: Token não encontrado no cache", ['token' => $token, 'key' => $cacheKey]);
            return Response::api(null, config('app.debug') ? 'Token inexistente ou ja consumido em tentativa anterior' : 'Acesso não autorizado', 403);
        }

        if (($storedData['context'] ?? null) !== $requiredContext) {
            Log::warning("Captcha Debug: Contexto divergente", ['expected' => $requiredContext, 'received' => $storedData['context']]);
            return Response::api(null, config('app.debug') ? "Contexto invalido: esperado {$requiredContext}, recebido ".($storedData['context'] ?? 'null') : 'Acesso não autorizado', 403);
        }

        $currentSessionId = session()->getId();
        $sessionMatches = isset($storedData['session_id']) && $storedData['session_id'] === $currentSessionId;

        $fingerprint = hash('sha256', ($request->ip() ?? '').'|'.($request->userAgent() ?? ''));
        $fingerprintMatches = isset($storedData['fingerprint']) && $storedData['fingerprint'] === $fingerprint;

        if (config('app.debug')) {
            Log::info("Captcha Debug: Verificando vínculos", [
                'session_stored' => $storedData['session_id'],
                'session_current' => $currentSessionId,
                'fingerprint_stored' => $storedData['fingerprint'],
                'fingerprint_current' => $fingerprint,
                'ip' => $request->ip(),
                'ua' => $request->userAgent()
            ]);
        }

        if (! $sessionMatches && ! $fingerprintMatches) {
            return Response::api(null, config('app.debug') ? 'Vinculo de segurança (Sessao/Fingerprint) nao coincide' : 'Acesso não autorizado', 403);
        }

        $response = $next($request);

        // Só consome o token se a requisição não falhou por validação ou erro de servidor.
        if ($response->getStatusCode() < 400) {
            Cache::forget($cacheKey);
        }

        return $response;
    }
}
