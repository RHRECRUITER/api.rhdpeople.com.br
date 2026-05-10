<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

use App\Enums\StatusUser;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids;

    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'password',
        'type',
        'status',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'type' => 'string',
        //'status' => 'string',
        'status' => StatusUser::class,
    ];

    // Relacionamento com Company (Tenant)
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'tenant_id');
    }

    //Relacionamento com License
    public function license(): HasOne{
        return $this->HasOne(License::class);
    }


    // Helpers de Nível de Acesso
    public function isSuperAdmin(): bool
    {
        return $this->type === 'super_admin';
    }

    public function isSystemAdmin(): bool
    {
        return $this->type === 'system_admin';
    }

    public function isCompanyAdmin(): bool
    {
        return $this->type === 'company_admin';
    }


    public function isCompanyUser(): bool
    {
        return $this->type === 'company_user';
    }

    /**
     * Helper de Verificação de Status
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}


