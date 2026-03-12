<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Importe as Interfaces e as Implementações
use App\Repositories\Contracts\CompanyRepositoryInterface;
use App\Repositories\Eloquent\EloquentCompanyRepository;
// Próximos passos (User, etc)
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\EloquentUserRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind da Interface com o Repository
        $this->app->bind(
            CompanyRepositoryInterface::class, 
            EloquentCompanyRepository::class
        );
        
        $this->app->bind(
            UserRepositoryInterface::class, 
            EloquentUserRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
