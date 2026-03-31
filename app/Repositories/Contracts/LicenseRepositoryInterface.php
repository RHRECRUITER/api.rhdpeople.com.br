<?php
namespace App\Repositories\Contracts;


use App\Enums\TypeLicense;
use App\Models\License;
Use App\Repositories\Contracts\TemplateRepositoryInterface;

interface LicenseRepositoryInterface extends TemplateRepositoryInterface{
    public function getByType(TypeLicense $type);
    public function countByType(TypeLicense $type);
    public function getAllUserByTypeLicense(TypeLicense $type);
}
?>
