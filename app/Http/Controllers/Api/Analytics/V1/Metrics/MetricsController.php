<?php

namespace App\Http\Controllers\Api\Analytics\V1\Metrics;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class MetricsController extends Controller
{
    public function index(Request $request)
    {
        // Mock / contrato - ainda hardcoded
        return Response::api([
            'success' => true,
            'data' => [
                'period' => [
                    'from' => $request->query('from', '2025-01-01'),
                    'to'   => $request->query('to', '2025-01-31'),
                    'page' => $request->query('page', '/landing'),
                ],

                'overview' => [
                    // Contagens
                    'total_page_views' => 1234,
                    'unique_visitors'  => 987,
                    'total_sessions'   => 850,

                    // Taxas (0..1)
                    'conversion_rate'  => 0.18, // 18%
                    'bounce_rate'      => 0.42, // 42%

                    // Tempo (segundos)
                    'avg_session_seconds' => 210, // 3m30s
                ],

                'forms' => [
                    'total_form_start'       => 120,
                    'total_form_submissions' => 45,

                    // Taxa de abandono (0..1)
                    'abandonment_rate'       => 0.62,
                ],

                'engagement' => [
                    // “Onde mais ficou” (defina como seção ou rota)
                    'top_section' => [
                        'section' => 'pricing',
                        'views'   => 780,
                    ],

                    // Scroll como percentual 0..100 (mais fácil de ler)
                    'avg_scroll_depth_pct' => 68,
                    'scroll_depth_distribution' => [
                        '0_25'   => 10,
                        '25_50'  => 20,
                        '50_75'  => 80,
                        '75_100' => 10,
                    ],
                ],
            ],
        ]);
    }
}
