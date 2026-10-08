<?php

namespace App\Services\Analytics\V1;

use App\Repositories\Eloquent\EloquentDailyMetricRepository;

class MetricsService
{
    public function __construct(
        protected EloquentDailyMetricRepository $repositoryMetrics
    ) {}

    private function calculateConversionRate(array $views_totals, array $conversions_totals): array
    {
        // Obtem a taxa de conversão, atráves do cálculo pela quantidade total de visitas e conversões
        return array_map(function ($views_total, $conversions_total) {
            $conversion_rate = $views_total ? round(($conversions_total / $views_total) * 100, 2) : 0;

            return $conversion_rate;

        }, $views_totals, $conversions_totals);
    }

    public function getOverview($start_date, $end_date)
    {
        // Formatação das datas
        $end_date = $end_date->format('Y-m-d');
        $start_date = $start_date->format('Y-m-d');

        // Coleta dos dados e conversão em vetores
        $datas = $this->repositoryMetrics->getByDateRange($start_date, $end_date);

        $dates = [];
        $view_totals = [];
        $conversions_totals = [];
        $bounce_rates = [];

        foreach ($datas as $data) {
            $dates[] = $data->date->format('Y-m-d');
            $view_totals[] = $data->total_views;
            $conversions_totals[] = $data->total_conversions;
            $bounce_rates[] = round($data->bounce_rate, 2);
        }

        $conversion_rates = $this->calculateConversionRate($view_totals, $conversions_totals);

        // Calculo das médias das métricas
        $sql = $this->repositoryMetrics->getAverageAllColuns($start_date, $end_date);

        $average_views = 0;
        $average_conversions = 0;
        $average_bounce_rates = 0;
        $average_conversion_rates = 0;

        if ($sql[0]->data_count) {
            $average_views = round($sql[0]->sum_views / $sql[0]->data_count);
            $average_conversions = round($sql[0]->sum_conversions / $sql[0]->data_count);
            $average_bounce_rates = round($sql[0]->sum_bounces / $sql[0]->data_count, 2);
            $average_conversion_rates = round(array_sum($conversion_rates) / count($conversion_rates), 2);
        }

        // Resposta
        return [
            'KPIs' => [
                'average_views' => $average_views,
                'average_conversions' => $average_conversions,
                'average_bounce_rates' => $average_bounce_rates,
                'average_conversion_rates' => $average_conversion_rates,
            ],
            'trends' => [
                'labels' => $dates,
                'datasets' => [
                    'label' => 'Views',
                    'data' => $view_totals,
                ],
                [
                    'label' => 'Conversions',
                    'data' => $conversions_totals,
                ],
                [
                    'label' => 'Conversion Rates',
                    'data' => $conversion_rates,
                ],
                [
                    'label' => 'Bounce Rates',
                    'data' => $bounce_rates,
                ],
            ],
        ];

    }
}
