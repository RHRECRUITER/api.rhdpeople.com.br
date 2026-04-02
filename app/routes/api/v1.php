<?php

use App\Http\Controllers\Api\Analytics\V1\Tracking\TrackController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Landing\Leads\V1\LeadsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API v1
|--------------------------------------------------------------------------
|
| Este arquivo concentra as rotas da versão v1 da API. Ele é carregado por
| routes/api.php dentro do prefixo "/api/v1".
|
| Aqui organizamos por módulos/prefixos. Exemplo:
| - /api/v1/landing/...
|
*/

// Endpoint para landing page  - pode ser usado para healthcheck ou outras rotas relacionadas.

Route::prefix('landing')->group(function () {
    // Healthcheck simples para verificar se a API está respondendo.
    Route::get('/health', fn() => Response::api(['status' => 'ok']));

    Route::post('/leads', [LeadsController::class, 'store'])
        ->middleware(['throttle:leads'])
        ->name('landing.leads');
});

// Rotas de Autenticação (Conceituais / Dev)
Route::prefix('auth')->group(function () {
    // Rota pública de login
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');

    // Rotas protegidas (exigem cookie de sessão válido do Sanctum)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me'])->name('auth.me');
        Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
    });
});

// Rota para métricas de analytics.

Route::prefix('analytics')->group(function () {
    // Rota para tracking de eventos - recebe dados de eventos do frontend.
    Route::post('/track', [TrackController::class, 'store'])
        ->middleware(['throttle:tracking'])
        ->name('analytics.track');

    //Rota ficticia para retornar uma visão geral de metricas.(ainda em etapa de ideação).
    Route::get('/overview', function (Request $request) {

        return Response::api([
            'success' => true,
            'data'    => [
                'period'     => [
                    'from' => $request->query('from', '2025-01-01'),
                    'to'   => $request->query('to', '2025-01-31'),
                    'page' => $request->query('page', '/landing'),
                ],

                'overview'   => [
                    // Contagens
                    'total_page_views'    => 1234,
                    'unique_visitors'     => 987,
                    'total_sessions'      => 850,

                                                   // Taxas (0..1)
                    'conversion_rate'     => 0.18, // 18%
                    'bounce_rate'         => 0.42, // 42%

                                                  // Tempo (segundos)
                    'avg_session_seconds' => 210, // 3m30s
                ],

                'forms'      => [
                    'total_form_start'       => 120,
                    'total_form_submissions' => 45,

                    // Taxa de abandono (0..1)
                    'abandonment_rate'       => 0.62,
                ],

                'engagement' => [
                    // “Onde mais ficou” (defina como seção ou rota)
                    'top_section'               => [
                        'section' => 'pricing',
                        'views'   => 780,
                    ],

                    // Scroll como percentual 0..100 (mais fácil de ler)
                    'avg_scroll_depth_pct'      => 68,
                    'scroll_depth_distribution' => [
                        '0_25'   => 10,
                        '25_50'  => 20,
                        '50_75'  => 80,
                        '75_100' => 10,
                    ],
                ],
            ],
        ]);
    })->middleware('auth:sanctum')->name('analytics.overview');
});
