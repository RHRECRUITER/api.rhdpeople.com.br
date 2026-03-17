<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids;

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
        'status' => 'string',
    ];

    // Relacionamento com Company (Tenant)
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'tenant_id');
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


