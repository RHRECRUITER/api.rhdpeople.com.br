<?php

namespace App\Repositories\Contracts\Syslog;

interface TenantAdminLogRepositoryInterface
{
    public function create(array $data);

    public function findById(string $id);

    /**
     * Histórico de ações administrativas sobre um tenant.
     */
    public function getByTenant(string $tenantId);
}
