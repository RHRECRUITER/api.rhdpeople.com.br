<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function __construct(protected User $model) {}

    public function all()
    {
        return $this->model->all();
    }

    public function findById(string $id)
    {
        return $this->model->findOrFail($id);
    }

    public function findByEmail(string $email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(string $id, array $data)
    {
        $user = $this->findById($id);
        $user->update($data);
        return $user;
    }

    public function delete(string $id)
    {
        return $this->model->destroy($id);
    }

    public function getByTenant(string $tenantId)
    {
        return $this->model->where('tenant_id', $tenantId)->get();
    }
}