<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;

/*
|--------------------------------------------------------------------------
| Bootstrap da aplicação
|--------------------------------------------------------------------------
|
| O bloco withExceptions(...) abaixo padroniza as respostas de erro em JSON
| para rotas da API ("/api/*"), atendendo critérios como:
| - 422 para validação
| - 404 para rota inexistente
| - 500 genérico (sem vazar stacktrace)
|
| Outros status comuns também são tratados (401/403/405/400/409/415/419/429).
|
*/

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(

        api: __DIR__ . "/../app/routes/api.php",
        web: __DIR__ . '/../app/routes/web.php',
        commands: __DIR__ . '/../app/routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'captcha.check' => \App\Http\Middleware\Security\v1\CheeckCaptchaToken::class,
        ]);
    })
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);
        $middleware->throttleApi('api');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Consideramos API qualquer request que comece com "/api/".
        $isApi = fn(Request $request) => $request->is('api/*');

        // Helper para montar a resposta de erro com o contrato padrão.
        $apiError = function (
            int $status,
            string $message,
            array $extra = []
        ) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'data'    => null,
                'meta'    => ['timestamp' => now()->toIso8601String()],
            ] + $extra, $status);
        };

        // Força JSON nas rotas de API (inclusive para 404/500 etc.).
        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) use ($isApi) {
            return $isApi($request);
        });

        // 422 - Erros de validação (request()->validate()).
        $exceptions->render(function (ValidationException $e, Request $request) use ($isApi, $apiError) {
            if (! $isApi($request)) {
                return null;
            }

            return $apiError(422, 'Validation failed', [
                'errors' => $e->errors(),
            ]);
        });

        // 401 - Não autenticado.
        $exceptions->render(function (AuthenticationException $e, Request $request) use ($isApi, $apiError) {
            if (! $isApi($request)) {
                return null;
            }

            return $apiError(401, 'Unauthenticated');
        });

        // 403 - Sem permissão.
        $exceptions->render(function (AuthorizationException $e, Request $request) use ($isApi, $apiError) {
            if (! $isApi($request)) {
                return null;
            }

            return $apiError(403, 'Forbidden');
        });

        // 404 - Rota/recurso não encontrado.
        $exceptions->render(function (NotFoundHttpException $e, Request $request) use ($isApi, $apiError) {
            if (! $isApi($request)) {
                return null;
            }

            return $apiError(404, 'Not found');
        });

        // 405 - Método HTTP não permitido (rota existe, mas método não).
        $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request) use ($isApi, $apiError) {
            if (! $isApi($request)) {
                return null;
            }

            return $apiError(405, 'Method not allowed');
        });

        // 400 - Request inválido (fora de validação).
        $exceptions->render(function (BadRequestHttpException $e, Request $request) use ($isApi, $apiError) {
            if (! $isApi($request)) {
                return null;
            }

            return $apiError(400, 'Bad request');
        });

        // 409 - Conflito (duplicidade/estado inválido).
        $exceptions->render(function (ConflictHttpException $e, Request $request) use ($isApi, $apiError) {
            if (! $isApi($request)) {
                return null;
            }

            return $apiError(409, 'Conflict');
        });

        // 415 - Content-Type não suportado.
        $exceptions->render(function (UnsupportedMediaTypeHttpException $e, Request $request) use ($isApi, $apiError) {
            if (! $isApi($request)) {
                return null;
            }

            return $apiError(415, 'Unsupported media type');
        });

        // 429 - Rate limit / throttle.
        $exceptions->render(function (TooManyRequestsHttpException $e, Request $request) use ($isApi, $apiError) {
            if (! $isApi($request)) {
                return null;
            }

            $retryAfter = $e->getHeaders()['Retry-After'] ?? null;

            return $apiError(429, 'Too many requests', [
                'meta' => array_filter([
                    'timestamp'   => now()->toIso8601String(),
                    'retry_after' => $retryAfter,
                ], fn($v) => $v !== null),
            ]);
        });

        // 419 - CSRF Token Mismatch (Falha de segurança Stateful).
        $exceptions->render(function (HttpException $e, Request $request) use ($isApi, $apiError) {
            if (! $isApi($request) || $e->getStatusCode() !== 419) {
                return null;
            }

            return $apiError(419, 'CSRF token mismatch. The request needs a valid XSRF-TOKEN.');
        });

        // 500 - Genérico, sem expor detalhes (stacktrace/mensagem interna).
        $exceptions->render(function (Throwable $e, Request $request) use ($isApi, $apiError) {
            if (! $isApi($request)) {
                return null;
            }

            $message = config('app.debug') ? $e->getMessage() : 'Internal server error';
            $extra   = config('app.debug') ? [
                'exception' => get_class($e),
                'file'      => $e->getFile(),
                'line'      => $e->getLine(),
            ] : [];

            return $apiError(500, $message, $extra);
        });
    })
    ->create();
