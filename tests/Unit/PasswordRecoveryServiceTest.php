<?php

namespace Tests\Unit;

use App\Jobs\Auth\PasswordRecovery\V1\SendPasswordRecoveryConfirmationJob;
use App\Jobs\Auth\PasswordRecovery\V1\SendPasswordRecoveryLinkJob;
use App\Models\PasswordRecoveryToken;
use App\Repositories\Contracts\PasswordRecoveryRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\Auth\V1\PasswordRecoveryService;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\TestCase;

class PasswordRecoveryServiceTest extends TestCase
{
    public function test_request_recovery_dispatches_link_job_without_exposing_user_existence(): void
    {
        Bus::fake();

        $users = Mockery::mock(UserRepositoryInterface::class);
        $users->shouldReceive('findByEmail')
            ->once()
            ->with('user@example.com')
            ->andReturn((object) [
                'id' => 'user-1',
                'email' => 'user@example.com',
                'name' => 'User Example',
            ]);

        $tokens = Mockery::mock(PasswordRecoveryRepositoryInterface::class);
        $tokens->shouldReceive('createToken')
            ->once()
            ->andReturn(tap(new PasswordRecoveryToken(), function (PasswordRecoveryToken $token): void {
                $token->token = '11111111-1111-1111-1111-111111111111';
            }));

        $service = new PasswordRecoveryService($users, $tokens);
        $service->requestRecovery('user@example.com');

        Bus::assertDispatched(SendPasswordRecoveryLinkJob::class, function (SendPasswordRecoveryLinkJob $job): bool {
            return $job->email === 'user@example.com'
                && $job->name === 'User Example'
                && $job->connection === 'redis'
                && $job->queue === 'emails';
        });
    }

    public function test_reset_password_updates_user_and_dispatches_confirmation_job(): void
    {
        Bus::fake();

        $user = new class
        {
            public string $email = 'user@example.com';
            public string $name = 'User Example';

            public function update(array $data): void
            {
                if (! Hash::check('NewPassw0rd!', $data['password'])) {
                    throw new \RuntimeException('Password was not hashed correctly.');
                }
            }
        };

        $token = tap(new PasswordRecoveryToken(), function (PasswordRecoveryToken $token) use ($user): void {
            $token->used_at = null;
            $token->expires_at = now()->addMinutes(5);
            $token->setRelation('user', $user);
        });

        $tokens = Mockery::mock(PasswordRecoveryRepositoryInterface::class);
        $tokens->shouldReceive('findByToken')
            ->once()
            ->with('11111111-1111-1111-1111-111111111111')
            ->andReturn($token);
        $tokens->shouldReceive('markAsUsed')
            ->once()
            ->with($token)
            ->andReturn($token);

        $users = Mockery::mock(UserRepositoryInterface::class);

        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(function (callable $callback) {
                return $callback();
            });

        $service = new PasswordRecoveryService($users, $tokens);
        $service->resetPassword('11111111-1111-1111-1111-111111111111', 'NewPassw0rd!');

        Bus::assertDispatched(SendPasswordRecoveryConfirmationJob::class, function (SendPasswordRecoveryConfirmationJob $job): bool {
            return $job->email === 'user@example.com'
                && $job->name === 'User Example'
                && $job->connection === 'redis'
                && $job->queue === 'emails';
        });
    }
}
