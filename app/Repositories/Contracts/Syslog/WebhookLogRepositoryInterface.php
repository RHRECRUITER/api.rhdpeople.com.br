<?php

namespace App\Repositories\Contracts\Syslog;

interface WebhookLogRepositoryInterface
{
    public function create(array $data);

    public function findById(string $id);

    /**
     * Checagem de idempotência — evita reprocessar o mesmo evento do gateway.
     */
    public function findByGatewayEventId(string $gatewayEventId);

    /**
     * Todos os logs de um mesmo fluxo de webhook (received → processed/failed).
     */
    public function getByCorrelationId(string $correlationId);
}
