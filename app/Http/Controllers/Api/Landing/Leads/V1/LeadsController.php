<?php

namespace App\Http\Controllers\Api\Landing\Leads\V1;

use App\Exceptions\DuplicateLeadException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Landing\Leads\V1\LeadsRequest;
use App\Services\Landing\LeadManagementService;
use Illuminate\Support\Facades\Response;

class LeadsController extends Controller
{
    public function store(LeadsRequest $request, LeadManagementService $service)
    {
        try {
            $lead = $service->registerLead($request->validated(), $request->ip());

            return Response::api($lead, 'Lead created', 201);
        } catch (DuplicateLeadException $e) {
            return Response::api(null, $e->getMessage(), 409);
        }
    }
}
