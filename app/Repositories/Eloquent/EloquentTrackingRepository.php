<?php

namespace App\Repositories\Eloquent;

use \Illuminate\Support\Str;
use App\Models\Tracking\Event;
use App\Repositories\Contracts\TrackingEventsRepositoryInterface;

class EloquentTrackingRepository implements TrackingEventsRepositoryInterface
{
    public function __construct(
        protected Event $model
    ){}

    public function logEvent(array $data)
    {
        return $this->model->create([
            'session_id' => $data['session_id'],
            'event_id'   => $data['event_id'] ?? Str::uuid(),
            'event_type' => $data['event_type'],
            'url'        => $data['url'],
            'payload'    => $data['payload'] ?? [],
            'created_at' => now(),
        ]);
    }

    public function getEventsBySession(string $sessionId)
    {
        return $this->model
            ->where('session_id', $sessionId)
            ->first();
    }

    public function getSessionSummary(string $sessionId)
    {
        $events = $this->getEventsBySession($sessionId);

        if ($events->isEmpty()) return null;

        $firstEvent = $events->first();
        $lastEvent = $events->last();

        return [
            'landing_page' => $firstEvent->url,
            'exit_page'    => $lastEvent->url,
            'started_at'   => $firstEvent->created_at,
            'ended_at'     => $lastEvent->created_at,
            'duration'     => $firstEvent->created_at->diffInSeconds($lastEvent->created_at),
            'total_events' => $events->count()
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

//public function logEvent(array $data);
//public function getEventsBySession(string $sessionId);