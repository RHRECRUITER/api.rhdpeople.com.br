<?php

namespace App\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

use App\Repositories\Contracts\TemplateRepositoryInterface;


abstract class EloquentTemplateRepository implements TemplateRepositoryInterface{
    /**
     *
     * @var Model
     */

    protected array $with = [];// coluna de relacionamento

    public function __construct(protected Model $model) {}

    public function create(array $attributes): Model{
        return $this->model->create($attributes);
    }
    public function update(string $id, array $attributes): Model{
        $model_found = $this->model->findOrFail($id);
        $model_found->update($attributes);
        return $model_found;
    }
    public function delete(string $id): bool{
        return $this->model->where('id',$id)->delete();
    }
    public function findAll(bool $paginate=true, int $perPage=15): Collection | LengthAwarePaginator
    {
        if($paginate){
            //True aplica paginação no resultado
            return $this->model->with($this->with)->paginate($perPage);
        }
        return $this->model->with($this->with)->get();
    }
    public function findByID(String $id): ?Model
    {
        return $this->model->firstOrFail();
    }
}

?>


