<?php

namespace App\Providers;

use App\Repositories\Contracts\AddOnRepositoryInterface;
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

use \App\Repositories\Contracts\PlanRepositoryInterface;
use \App\Repositories\Eloquent\EloquentPlanRepository;

use \App\Repositories\Contracts\LicenseRepositoryInterface;
use \App\Repositories\Eloquent\EloquentLicenseRepository;

use \App\Repositories\Contracts\ContractRepositoryInterface;
use App\Repositories\Contracts\ModuleRepositoryInterface;
use App\Repositories\Contracts\PlanTierRepositoryInterface;
use App\Repositories\Eloquent\EloquentAddOnRepository;
use \App\Repositories\Eloquent\EloquentContractRepository;
use App\Repositories\Eloquent\EloquentModuleRepository;
use App\Repositories\Eloquent\EloquentPlanTierRepository;

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

        /**
         * PLAN
         */
        $this->app->bind(
            PlanTierRepositoryInterface::class,
            EloquentPlanTierRepository::class
        );
        $this->app->bind(
            PlanRepositoryInterface::class,
            EloquentPlanRepository::class
        );
        $this->app->bind(
            ContractRepositoryInterface::class,
            EloquentContractRepository::class
        );
        $this->app->bind(
            LicenseRepositoryInterface::class,
            EloquentLicenseRepository::class
        );

        $this->app->bind(
            ModuleRepositoryInterface::class,
            EloquentModuleRepository::class
        );
        $this->app->bind(
            AddOnRepositoryInterface::class,
            EloquentAddOnRepository::class
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
