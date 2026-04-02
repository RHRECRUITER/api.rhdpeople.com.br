<?php
namespace App\Services\Analytics\V1;

use App\Domain\Analytics\EventSchema;
use App\Repositories\Contracts\TrackingEventsRepositoryInterface;
use App\Repositories\Contracts\SessionBlockRepositoryInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class IngestBatchService
{
    /**
     * Notas: Recebe um batch de eventos, valida e sanitiza os dados, e persiste ignorando duplicados. Retorna um resumo do processamento.
    - A validação e limpeza dos dados é feita antes da persistência para garantir a qualidade dos dados armazenados.
    - O logging detalhado permite monitorar o comportamento do sistema e identificar possíveis problemas ou padrões nos eventos recebidos (Apenas para debugging).
     */
    public function __construct(
        private TrackingEventsRepositoryInterface $trackingRepo,
        private SessionBlockRepositoryInterface $sessionBlockRepo
    ) {}
    public function ingest(array $payload): array
    {
        $events   = $payload['events'] ?? [];
        $accepted = count($events);

        $traceId = (string) Str::uuid();
        $role    = (string) config('analytics.tracking.default_role', 'public');
        $invalid = 0;

        $seenInBatch       = [];
        $uniqueEvents      = [];
        $duplicatesInBatch = 0;

        foreach ($events as $event) {
            $eventId = $event['event_id'] ?? null;

            // Se o event_id ja foi visto nesse batch, ele é contabilizado como duplicado e ignorado para inserção.
            if (isset($seenInBatch[$eventId])) {
                $duplicatesInBatch++;
                continue;
            }

            $seenInBatch[$eventId] = true;

            $type  = $event['type'] ?? null;
            $props = $event['props'] ?? [];

            if (! is_array($props)) {
                $invalid++;
                $event['props'] = [];

            } else {
                $allowedKeys = EventSchema::allowedPropsForType($type);
                $filtered    = array_intersect_key($props, array_flip($allowedKeys));

                foreach ($filtered as $key => $value) {
                    if (! is_string($value)) {
                        $value = (string) $value;
                    }

                    if (strlen($value) > 255) {
                        $value = substr($value, 0, 255);
                    }

                    $filtered[$key] = $value;
                }

                $event['props'] = $filtered;
            }

            //injeta os dados da raiz do payload no banco
            $event['session_id'] = $payload['session_id'] ?? null;
            $event['tenant_id']  = $payload['client_id'] ?? null;
            $event['user_id']    = auth('sanctum')->id();

            $uniqueEvents[] = $event;

        }
        // Atualiza a contagem de eventos validos após limpeza de duplicados e validação de props.
        $events = $uniqueEvents;

        $unique     = count($events);
        $typeCounts = [];
        foreach ($events as $event) {
            $type = $event['type'] ?? null;
            if (! is_string($type) || $type === '') {
                continue;
            }

            $typeCounts[$type] = ($typeCounts[$type] ?? 0) + 1;
        }

        // Loga o recebimento do batch para debug e monitoramento.
        Log::info('tracking.batch_received', [
            'trace_id'            => $traceId,
            'role'                => $role,
            'client_id'           => $payload['client_id'] ?? null,
            'session_id'          => $payload['session_id'] ?? null,
            'accepted'            => $accepted,
            'unique'              => $unique,
            'invalid'             => $invalid,
            'duplicates_in_batch' => $duplicatesInBatch,
            'event_type_counts'   => $typeCounts,
        ]);

        $result          = $this->trackingRepo->insertIgnoringDuplicates($events);
        $duplicatesTotal = $duplicatesInBatch + $result['duplicates'];

        if (! empty($payload['session_id'])) {
            $summary = $this->trackingRepo->getSessionSummary($payload['session_id']);
            if ($summary) {
                $summary['tenant_id'] = $payload['client_id'] ?? null;
                $this->sessionBlockRepo->updateOrCreateFromTracking($payload['session_id'], $summary);
            }
        }

        Log::info('tracking.batch_persisted', [
            'trace_id'            => $traceId,
            'role'                => $role,
            'inserted'            => $result['inserted'],
            'duplicates'          => $duplicatesTotal,
            'duplicates_in_db'    => $result['duplicates'],
            'duplicates_in_batch' => $duplicatesInBatch,
        ]);

        return [
            'trace_id'   => $traceId,
            'accepted'   => $accepted,
            'inserted'   => $result['inserted'],
            'duplicates' => $duplicatesTotal,
            'invalid'    => $invalid,
        ];
    }
}
