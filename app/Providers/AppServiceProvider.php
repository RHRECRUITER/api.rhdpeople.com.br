<?php
namespace App\Providers;

use App\Domain\Landing\LeadsStore;
use App\Infrastructure\Landing\InMemoryLeadsStore;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(LeadsStore::class, InMemoryLeadsStore::class);
    }
    public function boot(): void
    {
        /*
        |----------------------------------------------------------------------
        | JSON padrão da API (sucesso e erro "controlado")
        |----------------------------------------------------------------------
        |
        | Uso: return response()->api($data, $message, $status, $meta);
        |
        | Observação: os erros "automáticos" (422/404/500 etc.) são padronizados
        | separadamente em bootstrap/app.php no withExceptions(...).
        |
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

        /*
        |----------------------------------------------------------------------
        | Rate limit da API
        |----------------------------------------------------------------------
        |
        | Este limiter é usado por $middleware->throttleApi('api') em
        | bootstrap/app.php. O cache precisa estar configurado corretamente,
        | pois o throttle usa o cache para contar tentativas.
        |
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
    }
}
