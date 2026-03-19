<?php
namespace App\Repositories\Eloquent;

use \Illuminate\Support\Str;
use App\Models\EmailVerificationToken;
use App\Repositories\Contracts\EmailTokenRepositoryInterface;


class EloquentEmailTokenRepository implements EmailTokenRepositoryInterface
{

    protected $model;
    
    public function __construct(EmailVerificationToken $model)
    {
        $this->model = $model;
    }

    public function createToken(string $userId){
        $newToken = Str::random(64);;
        $expiresAt = now()->addHours(72); 

        return $this->model->create([
            'user_id'    => $userId,
            'token'      => $newToken,
            'expires_at' => $expiresAt,
            'created_at' => now(),
        ]);
    }

    public function findValidToken(string $token)
    {
        return $this->model->where('token', $token)
        ->whereNull('used_at')
        ->where('expires_at', '>', now())
        ->first();
    }
    
    public function invalidateToken(string $token)
    {
        $tokenToInvalidate = $this->findValidToken($token);
        if ($tokenToInvalidate)
        {
            $tokenToInvalidate->update([
                'used_at' => now()
            ]);
        }            
        return $tokenToInvalidate;
    }

}