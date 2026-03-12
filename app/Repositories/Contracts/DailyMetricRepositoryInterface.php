<?php

namespace App\Repositories\Contracts;

interface DailyMetricRepositoryInterface 
{
    public function updateOrCreateMetrics(array $data);
    public function getByDateRange(string $startDate, string $endDate);
    public function getByPage(string $pagePath);
}