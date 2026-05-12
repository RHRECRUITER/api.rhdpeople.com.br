<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface TemplateRepositoryInterface
{
    public function create(array $attributes): Model;

    public function update(string $id, array $attributes): Model;

    public function delete(string $id): bool;

    public function findAll(bool $paginate, int $perPage): Collection|LengthAwarePaginator;

    public function findById(string $id): ?Model;
}
