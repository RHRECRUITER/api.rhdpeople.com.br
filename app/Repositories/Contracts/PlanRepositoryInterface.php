<?php
namespace App\Repositories\Contracts;

use App\Models\Plan;
Use App\Repositories\Contracts\TemplateRepositoryInterface;

interface PlanRepositoryInterface extends TemplateRepositoryInterface{
    public function getAllUserInPlan(string $name_plan);
}
?>
