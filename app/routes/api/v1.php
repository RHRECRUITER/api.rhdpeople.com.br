<?php

use App\Http\Controllers\Api\Analytics\V1\Tracking\TrackController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Landing\Leads\V1\LeadsController;
use App\Http\Controllers\Api\Security\V1\CaptchaController;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

/**
 * API Routes - Version 1
 * Estrutura modular otimizada por pilhas de middleware (CSRF, Auth e Throttling).
 * * @author Luan Quevedo
 */

// ─── GRUPO 1: ROTAS PÚBLICAS (Sessão, Cookies e CSRF Ativos) ─────────────────────────
Route::middleware(['web'])->group(function () {

    // Sub-módulo: Autenticação Inicial
    Route::prefix('auth')->group(function () {
        Route::post('/register', [RegisterController::class, 'store']);
        Route::post('/login', [LoginController::class, 'store']);
    });

    // Sub-módulo: Security
    Route::prefix('security')->middleware('throttle:captcha')->group(function () {
        Route::get('/captcha/generate', [CaptchaController::class, 'getCaptcha'])->name('security.captcha.generate');
        Route::post('/captcha/verify', [CaptchaController::class, 'validateCaptcha'])->name('security.captcha.verify');
    });

    // Sub-módulo: Landing Page
    Route::prefix('landing')->group(function () {
        Route::get('/health', fn() => Response::api(['status' => 'ok']));
        Route::post('/leads', [LeadsController::class, 'store'])->middleware('captcha.check:leads_landing')->name('landing.leads');
    });
});

// ─── GRUPO 2: ROTAS PROTEGIDAS (Exigem Autenticação via Sessão/Sanctum) ───────────────
Route::middleware(['web', 'auth:sanctum'])->group(function () {

    // Revogação de Acesso
    Route::post('/auth/logout', [LoginController::class, 'destroy']);

    // Sub-módulo: Analytics (Limitador de taxa aplicado no nível do prefixo)
    Route::prefix('analytics')->middleware('throttle:tracking')->group(function () {
        Route::post('/track', [TrackController::class, 'store'])->name('analytics.track');
    });
});
