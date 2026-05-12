<?php

namespace App\Repositories\Eloquent;

use App\Models\Tracking\Event;
use App\Repositories\Contracts\TrackingEventsRepositoryInterface;
use Illuminate\Support\Facades\DB;

class EloquentTrackingRepository implements TrackingEventsRepositoryInterface
{
    public function insertIgnoringDuplicates(array $events): array
    {
        if (empty($events)) {
            return ['inserted' => 0, 'duplicates' => 0];
        }

        $records = [];
        foreach ($events as $event) {
            $records[] = [
                'id' => $event['event_id'],
                'session_id' => $event['session_id'] ?? null,
                'tenant_id' => $event['tenant_id'] ?? null,
                'user_id' => $event['user_id'] ?? null,
                'event_type' => $event['type'] ?? null,
                'url' => $event['page'] ?? null,
                'payload' => json_encode($event['props'] ?? []),
                'created_at' => date('Y-m-d H:i:s', strtotime($event['occurred_at'] ?? 'now')),
            ];
        }

        // insertOrIgnore faz a inserção em massa e pula os events_id já existentes
        $insertedCount = DB::connection('conn_rhdpeople_landingpage')
            ->table('tracking_events')
            ->insertOrIgnore($records);

        $duplicates = count($records) - $insertedCount;

        return [
            'inserted' => $insertedCount,
            'duplicates' => $duplicates,
        ];
    }

    public function __construct(
        protected Event $model
    ) {}

    public function getEventsBySession(string $sessionId)
    {
        return $this->model
            ->where('session_id', $sessionId)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function getSessionSummary(string $sessionId)
    {
        $events = $this->getEventsBySession($sessionId);

        if ($events->isEmpty()) {
            return null;
        }

        $firstEvent = $events->first();
        $lastEvent = $events->last();

        return [
            'landing_page' => $firstEvent->url,
            'exit_page' => $lastEvent->url,
            'started_at' => $firstEvent->created_at,
            'ended_at' => $lastEvent->created_at,
            'duration_seconds' => $firstEvent->created_at->diffInSeconds($lastEvent->created_at),
            'total_events' => $events->count(),
        ];
    }

    public function checkConversion(string $sessionId): bool
    {
        return $this->model
            ->where('session_id', $sessionId)
            ->where('event_type', 'form_submit_success')
            ->exists();
    }

    public function getAggregatedMetricsByDate($date)
    {
        return $this->model
            ->select('url as page_path')
            ->selectRaw('count(*) as total_views')
            ->selectRaw('count(distinct session_id) as total_sessions')
            ->whereDate('created_at', $date)
            ->groupBy('url')
            ->get();
    }

    public function getLastEventPayload(string $sessionId, string $eventType)
    {
        $event = $this->model
            ->where('session_id', $sessionId)
            ->where('event_type', $eventType)
            ->latest('created_at')
            ->first();

        return $event ? $event->payload : null;
    }
}

// public function logEvent(array $data);
// public function getEventsBySession(string $sessionId);
