<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Module extends Model
{
    use HasUuids;

    protected $table = 'modules';

    protected $fillable = [
        'key',
        'label',
        'extra_price',
        'description',
        'is_active',
    ];

    protected $casts = [
        'extra_price' => 'decimal:2',
        'is_active'   => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────────

    public function tierModules(): HasMany
    {
        return $this->hasMany(PlanTierModule::class, 'module_id');
    }

    public function planModules(): HasMany
    {
        return $this->hasMany(PlanModule::class, 'module_id');
    }

    // ─── Helpers ──────────────────────────────────────────────────

    /**
     * Verifica se o módulo é incluso (sem custo extra).
     *
     * @return boolean
     */
    public function isIncluded(): bool
    {
        return $this->extra_price === null;
    }

    /**
     * Retorna o preço formatado para exibição no front.
     * Retorna 'Incluso' se não tiver custo extra.
     *
     * @return string
     */
    public function displayPrice(): string
    {
        if ($this->isIncluded()) {
            return 'Incluso';
        }

        return '+R$ ' . number_format($this->extra_price, 2, ',', '.');
    }
}
