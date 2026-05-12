<?php

use App\Http\Controllers\Api\Analytics\V1\Tracking\TrackController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Landing\Leads\V1\LeadsController;
use App\Http\Controllers\Api\Security\V1\CaptchaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

/**
 * API Routes - Version 1
 * * Este arquivo define todos os endpoints da versão 1 da API RHDPeople.
 * A organização segue uma estrutura modular baseada em prefixos para facilitar
 * a manutenção e a aplicação de políticas de segurança (Throttling, Auth).
 *
 * * @author Luan Quevedo
 *
 * @since 2026-04
 */

/**
 * Módulo: Security
 * Gerenciamento de desafios visuais e tokens de validação.
 * Throttling: Limite de 10 tentativas por minuto (configurado em AppServiceProvider).
 */
Route::prefix('security')->group(function () {
    Route::get('/captcha/generate', [CaptchaController::class, 'getCaptcha'])
        ->middleware('throttle:captcha')
        ->name('security.captcha.generate');

    Route::post('/captcha/verify', [CaptchaController::class, 'validateCaptcha'])
        ->middleware('throttle:captcha')
        ->name('security.captcha.verify');
});

/**
 * Módulo: Landing Page
 * Endpoints públicos voltados para conversão e monitoramento de saúde da aplicação.
 */
Route::prefix('landing')->group(function () {
    // Healthcheck simples para verificar se a API está respondendo.
    Route::get('/health', fn () => Response::api(['status' => 'ok']));

    // Registro de Leads: Protegido por Captcha atômico com contexto de landing page.
    Route::post('/leads', [LeadsController::class, 'store'])
        ->middleware('captcha.check:leads_landing')
        ->name('landing.leads');
});

/**
 * Módulo: Auth
 * Gerenciamento de sessões e autenticação via Laravel Sanctum (Stateful).
 */
Route::prefix('auth')->group(function () {
    // Rota pública de login
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');

    // Rotas protegidas (exigem cookie de sessão válido do Sanctum)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me'])->name('auth.me');
        Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
    });
});

/**
 * Módulo: Analytics
 * Coleta de métricas comportamentais e exibição de indicadores de engajamento.
 */
Route::prefix('analytics')->group(function () {
    /**
     * Ingestão de Eventos: Recebe cliques, scroll e interações do frontend.
     * Throttling: 60 requisições por minuto para suportar múltiplos eventos de tracking.
     */
    Route::post('/track', [TrackController::class, 'store'])
        ->middleware(['throttle:tracking'])
        ->name('analytics.track');

    // Rota ficticia para retornar uma visão geral de metricas.(ainda em etapa de ideação).
    Route::get('/overview', function (Request $request) {

        return Response::api([
            'success' => true,
            'data' => [
                'period' => [
                    'from' => $request->query('from', '2025-01-01'),
                    'to' => $request->query('to', '2025-01-31'),
                    'page' => $request->query('page', '/landing'),
                ],

                'overview' => [
                    // Contagens
                    'total_page_views' => 1234,
                    'unique_visitors' => 987,
                    'total_sessions' => 850,

                    // Taxas (0..1)
                    'conversion_rate' => 0.18, // 18%
                    'bounce_rate' => 0.42, // 42%

                    // Tempo (segundos)
                    'avg_session_seconds' => 210, // 3m30s
                ],

                'forms' => [
                    'total_form_start' => 120,
                    'total_form_submissions' => 45,

                    // Taxa de abandono (0..1)
                    'abandonment_rate' => 0.62,
                ],

                'engagement' => [
                    // “Onde mais ficou” (defina como seção ou rota)
                    'top_section' => [
                        'section' => 'pricing',
                        'views' => 780,
                    ],

                    // Scroll como percentual 0..100 (mais fácil de ler)
                    'avg_scroll_depth_pct' => 68,
                    'scroll_depth_distribution' => [
                        '0_25' => 10,
                        '25_50' => 20,
                        '50_75' => 80,
                        '75_100' => 10,
                    ],
                ],
            ],
        ]);
    })->middleware('auth:sanctum')->name('analytics.overview');
});
