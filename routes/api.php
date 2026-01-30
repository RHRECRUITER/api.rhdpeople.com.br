<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas da API (entrada)
|--------------------------------------------------------------------------
|
| Observação: tudo que estiver neste arquivo já é carregado com o prefixo
| "/api" pelo Laravel (definido em bootstrap/app.php).
|
| Aqui nós só organizamos por versão, por exemplo:
| - /api/v1/...
|
*/
Route::prefix('v1')->group(function () {
    require base_path('routes/api/v1.php');
});
