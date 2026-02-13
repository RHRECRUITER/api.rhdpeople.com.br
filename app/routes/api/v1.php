<?php

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
Route::prefix('landing')->group(function () {
    // Healthcheck simples para verificar se a API está respondendo.
    Route::get('/health', fn() => Response::api([
        'status'  => 'Online',
        'version' => 'v1',
    ]));
});

Route::prefix('analytics')->group(function () {
    //Rota para coletar dados de analytics
    Route::post('/track', function () {
        return Response::api([
            'message' => 'Dados de analytics coletados com sucesso',
        ]);
    });
    //Rota para obter uma visão geral dos dados de analytics
    Route::get('/overview', 'AnalyticsController@overview');
});
