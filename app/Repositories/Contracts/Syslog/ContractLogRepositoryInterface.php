<?php

namespace App\Repositories\Contracts\Syslog;

interface ContractLogRepositoryInterface
{
    public function create(array $data);

    public function findById(string $id);

    /**
     * Histórico completo de mudanças de um contrato específico.
     */
    public function getByContract(string $contractId);

    /**
     * Último evento registrado para um contrato (ex: checar estado antes de nova ação).
     */
    public function lastEventForContract(string $contractId);
}
