<?php

namespace App\Repositories\Eloquent;

use App\Models\Contract;
use App\Repositories\Contracts\ContractRepositoryInterface;
use App\Repositories\Eloquent\EloquentTemplateRepository;


class EloquentContractRepository extends EloquentTemplateRepository implements ContractRepositoryInterface{
    public function __construct(Contract $model){
        parent::__construct($model);
    }
    public function filterAllExpiredContract(){
        //Retorna todos os contratos que venceu
        return $this->model->where('expiration_date', '<=', now())->get();
    }

}
