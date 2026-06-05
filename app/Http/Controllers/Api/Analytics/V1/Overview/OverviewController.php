<?php
namespace App\Http\Controllers\Api\Analytics\V1\Overview;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\Analytics\V1\MetricsService;
class OverviewController{
    public function __construct(MetricsService $metricsService){
        $this->metricsService = $metricsService;
    }
    public function index(Request $request){
        $validated_dates = $this->validateRequest($request);
        return response()->api($this->metricsService->getOverview($validated_dates['start_date'],$validated_dates['end_date']),'Dados obtidos com sucesso!');
    }

    private function validateRequest(Request $request){
        //Formata a requisição recebida em uma data válida.
        $date_interval = 7;
        $start_date = rescue(fn()=>Carbon::parse($request->query('start_date')),Carbon::today(),false);
        $end_date = rescue(fn()=> Carbon::parse($request->query('end_date')),Carbon::today()->addDays($date_interval),false);
        return[
            "start_date" => $start_date,
            "end_date" => $end_date,
        ];
    }

}
