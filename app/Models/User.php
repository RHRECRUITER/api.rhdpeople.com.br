<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'empresa_id',
        'tipo',
        'ativo',
        'telefone',
        'ultimo_acesso',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'ultimo_acesso' => 'datetime',
        'ativo' => 'boolean',
        'password' => 'hashed',
    ];

    // Relationships
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function logsAcesso()
    {
        return $this->hasMany(LogAcesso::class);
    }

    // Helpers
    public function isSuperAdmin()
    {
        return $this->tipo === 'super_admin';
    }

    public function isAdminEmpresa()
    {
        return $this->tipo === 'admin_empresa';
    }

    public function isUserDp()
    {
        return $this->tipo === 'user_dp';
    }
}
