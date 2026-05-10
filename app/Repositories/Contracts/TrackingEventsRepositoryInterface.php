<?php

namespace App\Repositories\Contracts;

interface TrackingEventsRepositoryInterface
{
    /**
     * Insere um lote de eventos, ignorando duplicados baseados na chave primária.
     */
    public function insertIgnoringDuplicates(array $events): array;

    public function getEventsBySession(string $sessionId);

    // --- Lógica de Agregação (session_blocks) ---
    /**
     * Recupera o resumo de uma sessão para popular session_blocks.
     * Útil para calcular duration_seconds, landing_page e exit_page.
     */
    public function getSessionSummary(string $sessionId);

    // --- Lógica de Conversão (leads / is_converted) ---
    /**
     * Verifica se houve algum evento de 'form_submit_success' em uma sessão específica.
     * Isso alimentará o campo 'is_converted' na session_blocks e o status no Lead.
     */

    public function checkConversion(string $sessionId): bool;

    // --- Lógica de BI (daily_metrics) ---
    /**
     * Agrupa o total de views e sessões únicas por path em uma data específica.
     * Essencial para popular a tabela daily_metrics em massa via Job.
     */
    public function getAggregatedMetricsByDate(\DateTime $date);

    // --- Lógica de Identificação (leads) ---
    /**
     * Busca o último payload de um evento específico (ex: form_start)
     * para ajudar a preencher o 'last_field_filled' na tabela leads.
     */
    public function getLastEventPayload(string $sessionId, string $eventType);
}
