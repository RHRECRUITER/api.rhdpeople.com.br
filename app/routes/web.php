<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ColaboradorController;
use App\Http\Middleware\TenantMiddleware;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - ATS DP
|--------------------------------------------------------------------------
*/

// Autenticação
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rotas protegidas (requer autenticação + tenant ativo)
Route::middleware(['auth', TenantMiddleware::class])->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Colaboradores
    Route::resource('colaboradores', ColaboradorController::class);

    // Outros módulos (a serem implementados)
    // Route::resource('ponto', PontoController::class);
    // Route::resource('ferias', FeriasController::class);
    // Route::resource('folha-pagamento', FolhaPagamentoController::class);
    // Route::resource('beneficios', BeneficioController::class);
    // Route::resource('documentos', DocumentoController::class);

});
