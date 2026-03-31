<?php

namespace App\Repositories\Contracts;

use App\Models\Contract;
Use App\Repositories\Contracts\TemplateRepositoryInterface;

interface ContractRepositoryInterface extends TemplateRepositoryInterface{
    public function filterAllExpiredContract();
}

?>
