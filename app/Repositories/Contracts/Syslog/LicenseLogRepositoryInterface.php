<?php

namespace App\Repositories\Contracts\Syslog;

interface LicenseLogRepositoryInterface
{
    public function create(array $data);

    public function findById(string $id);

    /**
     * Histórico completo de mudanças de uma licença específica.
     */
    public function getByLicense(string $licenseId);
}
