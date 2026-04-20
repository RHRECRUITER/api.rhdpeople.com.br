<?php
namespace App\Providers;

use App\Domain\Landing\LeadsStore;
use App\Infrastructure\Landing\InMemoryLeadsStore;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

/**
 * Class AppServiceProvider
 * * Provedor central de serviços da aplicação.
 * Responsável pelo bootstrap de componentes fundamentais, incluindo:
 * 1. Injeção de Dependência (Bindings de Interfaces).
 * 2. Extensão de classes core (Macros de Resposta).
 * 3. Políticas de segurança de tráfego (Rate Limiting).
 * * @package App\Providers
 * @author Luan Quevedo
 */
class AppServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        $this->app->bind(LeadsStore::class, InMemoryLeadsStore::class);
    }
    public function boot(): void
    {
        /**
         * Define o padrão de contrato JSON para todas as respostas da API.
         * Adiciona o macro 'api' à Facade Response.
         * * Uso: return response()->api($data, $message, $status, $meta);
         * * @return void
         */
        Response::macro('api', function (
            $data = null,
            string $message = 'OK',
            int $status = 200,
            array $meta = []
        ) {
            return response()->json([
                'success' => $status < 400,
                'message' => $message,
                'data'    => $data,
                'meta'    => $meta + ['timestamp' => now()->toIso8601String()],
            ], $status);
        });

        /**
         * Configura as políticas de aceleração (Throttle) da aplicação.
         * Protege contra ataques de força bruta e DoS (Denial of Service).
         * * @return void
         */
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(10)->by((
                $request->user()?->id ?: $request->ip()
            ));
        });

        RateLimiter::for('tracking', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip());
        });

        RateLimiter::for('leads', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });
        RateLimiter::for('captcha', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });
    }
}
