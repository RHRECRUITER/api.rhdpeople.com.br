<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Importe as Interfaces e as Implementações
use App\Repositories\Contracts\CompanyRepositoryInterface;
use App\Repositories\Eloquent\EloquentCompanyRepository;

use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\EloquentUserRepository;

use App\Repositories\Contracts\EmailTokenRepositoryInterface;
use App\Repositories\Eloquent\EloquentEmailTokenRepository;

use App\Repositories\Contracts\PasswordRecoveryRepositoryInterface;
use App\Repositories\Eloquent\EloquentPasswordRecoveryRepository;

use App\Repositories\Contracts\EmployeeInviteRepositoryInterface;
use App\Repositories\Eloquent\EloquentEmployeeInviteRepository;

use App\Repositories\Contracts\TrackingEventsRepositoryInterface;
use App\Repositories\Eloquent\EloquentTrackingRepository;

use App\Repositories\Contracts\SessionBlockRepositoryInterface;
use App\Repositories\Eloquent\EloquentSessionBlockRepository;

use \App\Repositories\Contracts\DailyMetricRepositoryInterface;
use \App\Repositories\Eloquent\EloquentDailyMetricRepository;

use \App\Repositories\Contracts\LeadRepositoryInterface;
use \App\Repositories\Eloquent\EloquentLeadRepository;



class RepositoryServiceProvider extends ServiceProvider
{
    
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

        /**
         * EMAIL TOKEN
         * RECOVERY PASSWORD
         * EMPLOYEE INVITE
         */
        $this->app->bind(
            EmailTokenRepositoryInterface::class, 
            EloquentEmailTokenRepository::class
        );
        $this->app->bind(
            PasswordRecoveryRepositoryInterface::class, 
            EloquentPasswordRecoveryRepository::class
        );
        $this->app->bind(
            EmployeeInviteRepositoryInterface::class, 
            EloquentEmployeeInviteRepository::class
        );


        /**
         * TRACKING
         */
        $this->app->bind(
            TrackingEventsRepositoryInterface::class,
            EloquentTrackingRepository::class
        );
        $this->app->bind(
            SessionBlockRepositoryInterface::class,
            EloquentSessionBlockRepository::class
        );
        $this->app->bind(
            DailyMetricRepositoryInterface::class,
            EloquentDailyMetricRepository::class
        );
        $this->app->bind(
            LeadRepositoryInterface::class,
            EloquentLeadRepository::class
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
