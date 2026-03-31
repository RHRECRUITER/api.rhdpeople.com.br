<?php

namespace App\Repositories\Eloquent;

use App\Models\Plan;
use App\Models\User;
use App\Repositories\Contracts\PlanRepositoryInterface;
use App\Repositories\Eloquent\EloquentTemplateRepository;


class EloquentPlanRepository extends EloquentTemplateRepository implements PlanRepositoryInterface{
    public function __construct(Plan $model){
        parent::__construct($model);
    }
    /**
     *
     */
    public function getAllUserInPlan(string $name_plan){
        //Retorna quais usuarios estão utilizando determinado plano
        return User::whereRelation('license.plan', 'type_plan', $name_plan)->pluck('id');
    }



}

?>
