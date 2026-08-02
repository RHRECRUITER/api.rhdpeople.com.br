<?php

namespace App\Repositories\Contracts\Syslog;

interface AuthenticationLogRepositoryInterface
{
    public function create(array $data);

    public function findById(string $id);

    /**
     * Conta tentativas falhas de um usuário desde um instante — base para lockout.
     */
    public function countFailedAttemptsSince(string $userId, \Carbon\Carbon $since): int;

    /**
     * Retorna o último login bem-sucedido de um usuário.
     */
    public function lastSuccessfulLogin(string $userId);
}
