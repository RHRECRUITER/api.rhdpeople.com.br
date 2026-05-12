<?php

namespace App\Http\Controllers\Api\Security\V1;

use App\Http\Controllers\Controller;
use App\Services\Security\V1\CaptchaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Class CaptchaController
 * Responsável por fornecer desafios visuais e converter validações bem-sucedidas
 * em tokens de contexto atômicos para operações sensíveis.
 *
 * @author Luan Quevedo
 */
class CaptchaController extends Controller
{
    /** @var CaptchaService Instância do serviço de manipulação de imagens GD */
    protected CaptchaService $captchaService;

    public function __construct(CaptchaService $captchaService)
    {
        $this->captchaService = $captchaService;
    }

    /**
     * Endpoint para geração de novo Captcha.
     * * Retorna uma chave única (UUIDv7) e a imagem codificada em Base64.
     *
     * * @return JsonResponse
     */
    public function getCaptcha()
    {
        $data = $this->captchaService->generateCaptcha();

        return Response::api([
            'captcha_key' => $data['key'],
            'captcha_image' => $data['image'],
        ], 'Captcha gerado com sucesso');
    }

    /**
     * Valida o captcha e gera um Token de Validação de Contexto.
     * * Caso o código esteja correto, um token aleatório de 64 caracteres é gerado
     * e armazenado no Redis, vinculado à sessão do usuário e ao contexto da operação solicitada.
     *
     * @return JsonResponse
     *
     * @throws ValidationException
     */
    public function validateCaptcha(Request $request)
    {
        $request->validate([
            'captcha_key' => 'required|string',
            'captcha_code' => 'required|string|size:5',
            'context' => 'required|string',
        ]);

        $isValid = $this->captchaService->validateCaptcha(
            $request->captcha_key,
            $request->captcha_code
        );
        if (! $isValid) {
            return Response::api(null, 'Captcha invalido ou expirado', 422);
        }

        $token = Str::random(64);

        // Persistência do token com vínculo de sessão e contexto
        Cache::put("validated_action:{$token}", [
            'session_id' => session()->getId(),
            'context' => $request->context,
        ], now()->addMinutes(5));

        // Retornando o token de sucesso
        return Response::api([
            'validation_token' => $token,
        ], 'Captcha validado');
    }
}
