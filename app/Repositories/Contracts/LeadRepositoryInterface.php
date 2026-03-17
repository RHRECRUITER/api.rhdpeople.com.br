<?php

namespace App\Repositories\Contracts;

interface LeadRepositoryInterface 
{
    public function updateOrCreateLead(string $sessionId, array $data);
    public function markAsConverted(string $sessionId, array $finalData = []);
    public function getConvertedLeads(int $perPage = 15);
}