<?php

namespace App\Http\Controllers\Api\Analytics\V1\Tracking;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tracking\TrackBatchRequest;
use App\Services\Analytics\V1\IngestBatchService;
use Illuminate\Support\Facades\Response;

class TrackController extends Controller
{
    public function store(TrackBatchRequest $request, IngestBatchService $service)
    {
        $result = $service->ingest($request->validated());

        return Response::api([
            'trace_id' => $result['trace_id'],
            'accepted' => $result['accepted'],
            'inserted' => $result['inserted'],
            'duplicates' => $result['duplicates'],
            'invalid' => $result['invalid'],
        ]);
    }
}
