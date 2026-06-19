<?php

namespace App\Repositories\Eloquent;

use App\Models\Tracking\DailyMetric;
use App\Repositories\Contracts\DailyMetricRepositoryInterface;

class EloquentDailyMetricRepository implements DailyMetricRepositoryInterface
{
    public function __construct(
        protected DailyMetric $model
    ) {}

    public function updateOrCreateMetrics(array $data)
    {
        return $this->model->updateOrCreate(
            [
                'date' => $data['date'],
                'page_path' => $data['page_path'],
            ],
            [
                'total_views' => $data['total_views'] ?? 0,
                'total_sessions' => $data['total_sessions'] ?? 0,
                'total_conversions' => $data['total_conversions'] ?? 0,
                'bounce_rate' => $data['bounce_rate'] ?? 0,
            ]
        );
    }

    public function getByDateRange(string $startDate, string $endDate)
    {
        return $this->model
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'asc')
            ->get();
    }

    public function getByPage(string $pagePath)
    {
        return $this->model
            ->where('page_path', $pagePath)
            ->orderBy('date', 'desc')
            ->get();
    }

    public function getAverageAllColuns(string $startDate, string $endDate){
        return $this->model::selectRaw('COUNT(*) AS data_count, SUM(total_views) AS sum_views, SUM(total_conversions) AS sum_conversions, SUM(bounce_rate) AS sum_bounces')
        ->whereBetween('date', [$startDate, $endDate])
        ->orderBy('date', 'asc')
        ->get();
    }
}
