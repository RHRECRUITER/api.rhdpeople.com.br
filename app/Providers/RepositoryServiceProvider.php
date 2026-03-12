<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Importe as Interfaces e as Implementações
use App\Repositories\Contracts\CompanyRepositoryInterface;
use App\Repositories\Eloquent\EloquentCompanyRepository;

use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\EloquentUserRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // BIND DA INTERFACE COM O REPOSITORY
        /**
         * COMPANY
         */
        $this->app->bind(
            CompanyRepositoryInterface::class, 
            EloquentCompanyRepository::class
        );
        /**
         * USER
         */
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
