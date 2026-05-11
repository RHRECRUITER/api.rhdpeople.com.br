<?php

namespace App\Models;

use App\Enums\TypeLicense;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class License extends Model
{
    use HasUuids;

    protected $table = 'licenses';

    protected $fillable = [
        'plan_id',
        'user_id',
        'type_license',
        'is_active',
    ];

    protected $casts = [
        'type_license' => TypeLicense::class,
        'is_active'    => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────────

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ─── Helpers ──────────────────────────────────────────────────

    /**
     * Verifica se a licença pertence ao dono do plano.
     *
     * @return boolean
     */
    public function isDono(): bool
    {
        return $this->type_license === TypeLicense::DONO;
    }

    /**
     * Verifica se a licença é operacional.
     *
     * @return boolean
     */
    public function isOperacional(): bool
    {
        return $this->type_license === TypeLicense::OPERACIONAL;
    }

    /**
     * Verifica se a licença é de colaborador.
     *
     * @return boolean
     */
    public function isColaborador(): bool
    {
        return $this->type_license === TypeLicense::COLABORADOR;
    }

    /**
     * Verifica se a licença pode ser desativada.
     * O dono nunca pode ser desativado.
     *
     * @return boolean
     */
    public function canBeDeactivated(): bool
    {
        return !$this->isDono();
    }

    /**
     * Retorna o status inferido da licença.
     * Não há coluna status no banco — é calculado a partir de is_active.
     *
     * @return string
     */
    public function status(): string
    {
        if ($this->isDono()) {
            return 'active';
        }

        return $this->is_active ? 'active' : 'inactive';
    }
}
