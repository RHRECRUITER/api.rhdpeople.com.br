<?php

namespace App\Models;

use App\Enums\TypeLicense;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasUuids, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function licenses(): HasMany
    {
        return $this->hasMany(License::class, 'user_id');
    }

    /**
     * Retorna a empresa atual que o usuário está acessando
     * com base na sua licença ativa.
     */
    public function currentCompany()
    {
        $activeLicense = $this->licenses()->with('plan.tenant')->where('is_active', true)->first();

        return $activeLicense ? $activeLicense->plan->tenant : null;
    }

    // ─── HELPERS DE NÍVEL DE ACESSO ───────────────────────────────────

    /**
     * Acesso Interno Total (Equipe RHDPeople / Devs)
     */
    public function isSuperAdmin(): bool
    {
        // Se o e-mail for do domínio corporativo, ele tem acesso livre.
        // Isso facilita os seus testes sem precisar mexer no banco de dados.
        return str_ends_with($this->email, '@rhdpeople.com.br');
    }

    public function isSystemAdmin(): bool
    {
        // Por ora, deixamos igual ao Super Admin.
        // No futuro você pode validar se está num array específico de e-mails.
        return $this->isSuperAdmin();
    }

    public function isCompanyAdmin(): bool
    {
        // Procura se possui uma licença ATIVA do tipo DONO
        return $this->licenses()
            ->where('is_active', true)
            ->where('type_license', TypeLicense::DONO->value)
            ->exists();
    }

    public function isCompanyUser(): bool
    {
        return $this->licenses()
            ->where('is_active', true)
            ->exists();
    }
}
