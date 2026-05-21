<?php

namespace Tests\Feature;

/**
 * Interface para o repositório de Leads usada nos testes.
 */
interface LeadsStore
{
    public function create(array $data): array;
}
