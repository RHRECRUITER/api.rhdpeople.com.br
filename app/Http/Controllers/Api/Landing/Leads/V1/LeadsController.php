<?php

namespace App\Http\Controllers\Api\Landing\Leads\V1;

use App\Domain\Landing\LeadsStore;
use App\Http\Controllers\Controller;
use App\Http\Requests\Landing\Leads\V1\LeadsRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class LeadsController extends Controller
{
    public function store(LeadsRequest $request, LeadsStore $store)
    {
        $payload = $request->validated();
        $email = (string) ($payload['corporate_email'] ?? '');
        $key = 'leads:dedupe:email'.sha1($email);

        if (Cache::has($key)) {
            return Response::api(null, 'Lead already submitted recently', 409);
        }
        Cache::put($key, true, now()->addHour());
        // log apenas para finalidade de desenvolvimento.
        Log::info('leads.demo_requested', [
            'name' => $payload['name'] ?? null,
            'company_name' => $payload['company_name'] ?? null,
            'corporate_email' => $payload['corporate_email'] ?? null,
            'phone' => $payload['phone'] ?? null,
            'modules' => $payload['objective'] ?? null,
            'employees' => $payload['number_employees'] ?? null,
            'ip' => $request->ip(),
        ]);
        $result = $store->create($payload);

        return Response::api($result, 'Lead created', 201);
    }
}
