<?php
Namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\LicenseRepositoryInterface;
use App\Repositories\Eloquent\EloquentTemplateRepository;
use App\Models\License;
use App\Enums\TypeLicense;

use App\Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentLicenseRepository extends EloquentTemplateRepository implements LicenseRepositoryInterface{
    protected array $with = ['plan', 'user'];//Retorna as colunas da relação

    public function __construct(License $model){
        parent::__construct($model);
    }
    public function getByType(TypeLicense|string $type) {
        if(is_string($type)){
            $type = TypeLicense::from($type);
        }
        return $this->model->with($this->with)
            ->where('type_license',$type)
            ->paginate(15);
    }
    public function countByType(TypeLicense|string $type){
        //Retorna a quantidade do tipo de licença solicitado
        if(is_string($type)){
            $type = TypeLicense::from($type);
        }
        return $this->model->with($this->with)
            ->where('type_license',$type)
            ->count();
    }
    public function getAllUserByTypeLicense(TypeLicense|string $type){
       //Retorna os usuarios que tenham o tipo de licença solicitado
       if(is_string($type)){
            $type = TypeLicense::from($type);
       }
       return $this->mode->where('type_license',$type)->pluck('user_id');
    }
}

?>
