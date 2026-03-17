<?php

namespace App\Repositories\Contracts;

interface SessionBlockRepositoryInterface 
{
    /**
     * Cria ou atualiza o bloco de sessão baseado nos dados de tracking.
     */
    public function updateOrCreateFromTracking(string $sessionId, array $data);

    /**
     * Marca a sessão como convertida (ex: após preenchimento de lead).
     */
    public function markAsConverted(string $sessionId): bool;

    /**
     * Busca uma sessão específica pelo ID do navegador.
     */
    public function findBySessionId(string $sessionId);

    /**
     * Retorna sessões recentes que ainda não foram convertidas.
     */
    public function getRecentUnconverted(string $tenantId, int $limit = 10);
}