<?php

namespace App\Repositories\Contracts;

interface LicenseRepositoryInterface
{
    public function all();

    public function findById(string $id);

    public function create(array $data);

    public function update(string $id, array $data);

    public function delete(string $id);

    /**
     * Retorna todas as licenças de um plano por tipo.
     */
    public function getByType(string $planId, string $type);

    /**
     * Conta licenças de um plano por tipo.
     */
    public function countByType(string $planId, string $type);

    /**
     * Retorna todos os usuários com um tipo de licença específico.
     */
    public function getAllUsersByTypeLicense(string $planId, string $type);

    /**
     * Conta licenças ativas de um plano.
     */
    public function countActiveByPlan(string $planId);

    /**
     * Retorna a licença do dono de um plano.
     */
    public function getDonoByPlan(string $planId);

    /**
     * Desativa uma licença — não permitido para licenças do tipo dono.
     */
    public function deactivate(string $id);

    /**
     * Ativa uma licença.
     */
    public function activate(string $id);
}
