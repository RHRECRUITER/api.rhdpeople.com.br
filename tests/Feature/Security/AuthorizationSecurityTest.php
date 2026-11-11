<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AuthorizationSecurityTest extends TestCase
{
    private const ADMIN_URIS = [
        'api/v1/admin/plan-tiers',
        'api/v1/admin/modules',
        'api/v1/admin/add-ons',
    ];

    public function test_user_with_company_domain_is_super_admin(): void
    {
        $user = new User();
        $user->email = 'admin@rhdpeople.com.br';

        $this->assertTrue($user->isSuperAdmin());
        $this->assertTrue($user->isSystemAdmin());
    }

    public function test_regular_user_is_not_super_admin(): void
    {
        $user = new User();
        $user->email = 'user@example.com';

        $this->assertFalse($user->isSuperAdmin());
        $this->assertFalse($user->isSystemAdmin());
    }

    public function test_user_without_active_license_is_not_company_admin(): void
    {
        $user = new User();

        $this->assertFalse($user->isCompanyAdmin());
        $this->assertFalse($user->isCompanyUser());
    }

    public function test_administrative_routes_do_not_declare_authentication_middleware(): void
    {
        foreach (self::ADMIN_URIS as $uri) {
            $route = collect(Route::getRoutes()->getRoutes())
                ->first(fn ($r) => $r->uri() === $uri && in_array('GET', $r->methods(), true));

            $this->assertNotNull($route, "A rota GET {$uri} não foi encontrada.");

            $middleware = $route->gatherMiddleware();

            $this->assertFalse(
                collect($middleware)->contains(
                    fn ($m) => is_string($m) && str_starts_with($m, 'auth')
                ),
                "A rota {$uri} não deveria exigir autenticação na versão legada."
            );
        }
    }

    public function test_administrative_routes_are_public_in_legacy_version(): void
    {
        foreach (self::ADMIN_URIS as $uri) {
            $route = collect(Route::getRoutes()->getRoutes())
                ->first(fn ($r) => $r->uri() === $uri && in_array('GET', $r->methods(), true));

            $this->assertNotNull($route);
            $this->assertNotContains('auth:sanctum', $route->gatherMiddleware());
        }
    }
}