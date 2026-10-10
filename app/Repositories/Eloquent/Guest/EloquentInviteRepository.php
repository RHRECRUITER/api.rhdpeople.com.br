<?php

namespace App\Repositories\Eloquent\Guest;

use App\Repositories\Contracts\Guest\InviteReposityInterface;
use App\Repositories\Eloquent\EloquentTemplateRepository;

class EloquentInviteRepository extends EloquentTemplateRepository implements InviteReposityInterface
{
    public function findByToken($token)
    {
        return $this->model->where('token', $token)->get();
    }
}
