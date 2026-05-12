<?php

namespace App\Repositories\Contracts;

interface PlanTierRepositoryInterface
{
    public function all();

    public function findById(string $id);

    public function create(array $data);

    public function update(string $id, array $data);

    public function delete(string $id);

    /**
     * Retorna todos os tiers ativos para exibição no front.
     *
     * @return void
     */
    public function getAllActive();

    /**
     * Retorna o tier com seus módulos e add-ons para montar o card.
     *
     * @return void
     */
    public function getWithModulesAndAddOns(string $id);
}
