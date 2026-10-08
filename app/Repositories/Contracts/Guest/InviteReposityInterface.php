<?php

namespace App\Repositories\Contracts\Guest;

use App\Repositories\Contracts\TemplateRepositoryInterface;

interface InviteReposityInterface extends TemplateRepositoryInterface
{
    public function findByToken(string $token);
}
