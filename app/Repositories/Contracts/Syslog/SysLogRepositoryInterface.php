<?php

namespace App\Repositories\Contracts\Syslog;

interface SysLogRepositoryInterface
{
    /**
     * Cria um registro de log. Não expõe update/delete — sys_logs é append-only.
     */
    public function create(array $data);

    public function findById(string $id);

    /**
     * Lista logs de um tenant (ou globais, se $tenantId for null), paginado.
     */
    public function paginateByTenant(?string $tenantId, int $perPage = 25);

    /**
     * Filtra logs por discriminador STI (authentication, contract, license...).
     */
    public function getByType(string $type, ?string $tenantId = null);

    /**
     * Retorna todos os logs vinculados a um mesmo fluxo (request/job).
     */
    public function getByCorrelationId(string $correlationId);

    /**
     * Retorna o histórico de auditoria de uma entidade específica (polimórfico).
     */
    public function getByAuditable(string $auditableType, string $auditableId);

    /**
     * Filtra logs por severidade — útil para monitoramento de erros/warnings.
     */
    public function getBySeverity(string $severity, ?string $tenantId = null);

    /**
     * Retorna logs de um usuário específico (ações realizadas por ele).
     */
    public function getByUser(string $userId);
}
