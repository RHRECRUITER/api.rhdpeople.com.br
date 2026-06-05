<?php

use App\Http\Controllers\Api\Analytics\V1\Tracking\TrackController;
use App\Http\Controllers\Api\Analytics\V1\Overview\OverviewController;
use App\Http\Controllers\Api\Landing\Leads\V1\LeadsController;
use App\Http\Controllers\Api\Security\V1\CaptchaController;
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
    Route::get('/health', fn() => Response::api(['status' => 'ok']));

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

    // Rotas protegidas (exigem cookie de sessão válido do Sanctum)
    Route::middleware('auth:sanctum')->group(function () {
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
    //Rota destinada a retorno de dados para alimentação do dashboard
    Route::middleware('auth:sanctum')
        ->prefix('/dashboard')
        ->group(function (){
            Route::get('/overview', [OverviewController::class,'index'])->name('dashboard.overview');
    });
});
