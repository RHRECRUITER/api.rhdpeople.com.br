<?php

namespace App\Http\Controllers\Api\Analytics\V1\Tracking;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use App\Http\Requests\Tracking\TrackBatchRequest;
use App\Services\Analytics\V1\IngestBatchService;

class TrackController extends Controller
{
    public function store(TrackBatchRequest $request, IngestBatchService $service)
    {
        $result = $service->ingest($request->validated());


        return Response::api([
            'trace_id'  => $result['trace_id'],
            'accepted' => $result['accepted'],
            'inserted' => $result['inserted'],
            'duplicates' => $result['duplicates'],
            'invalid' => $result['invalid'],
        ]);
    }
}
