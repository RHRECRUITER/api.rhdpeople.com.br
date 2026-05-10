<?php
namespace App\Http\Middleware\Security\v1;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;

/**
 * Class CheeckCaptchaToken
 * * Middleware de segurança responsável por interceptar requisições em rotas protegidas
 * e validar a existência, validade e integridade de um Token de Contexto.
 * * Este componente garante operações de uso único e previne
 * ataques de Cross-Site Request Forgery (CSRF) e Bypass de Captcha.
 * @author Luan Quevedo
 */
class CheeckCaptchaToken
{
    /**
     * Manipula a requisição de entrada.
     * * O fluxo de validação segue os seguintes critérios:
     * 1. Presença do Token: Verifica se o token foi enviado via Header ou Body.
     * 2. Atomicidade (Pull): Recupera o token do Cache e o remove instantaneamente.
     * 3. Validação de Contexto: Garante que o token gerado é para a rota correta.
     * 4. Validação de Sessão: Verifica se o proprietário do token é o mesmo da sessão atual.
     * * @param Request $request Requisição HTTP recebida.
     * @param Closure $next Próximo fechamento no pipeline do pipeline.
     * @param string $requiredContext A finalidade exigida para a rota.
     * @return mixed Resposta da API em caso de erro (403) ou prosseguimento da requisição.
     */
    public function handle(Request $request, Closure $next, string $requiredContext)
    {
        // Prioriza a leitura do token pelo Header customizado
        $token = $request->header('X-Validation-Token') ?? $request->validation_token;

        if (! $token) {
            return Response::api(null, 'Token de segurança ausente', 403);
        }
        $storedData = Cache::pull("validated_action:{$token}");
        if (! $storedData) {
            return Response::api(null, 'Token expirado ou já utilizado', 403);
        }
        if ($storedData['context'] !== $requiredContext) {
            return Response::api(null, 'Ação não autorizada para este token', 403);
        }
        if ($storedData['session_id'] !== session()->getId()) {
            return Response::api(null, 'Sessão inválida', 403);
        }
        return $next($request);
    }
}
