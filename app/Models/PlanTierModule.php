<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanTierModule extends Model
{
    use HasUuids;

    protected $table = 'plan_tier_modules';

    protected $fillable = [
        'plan_tier_id',
        'module_id',
        'is_included',
        'is_available',
    ];

    protected $casts = [
        'is_included'  => 'boolean',
        'is_available' => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────────

    public function planTier(): BelongsTo
    {
        return $this->belongsTo(PlanTier::class, 'plan_tier_id');
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class, 'module_id');
    }

    // ─── Helpers ──────────────────────────────────────────────────

    /**
     * Verifica se o módulo está incluso neste tier (sem custo extra).
     *
     * @return boolean
     */
    public function isIncluded(): bool
    {
        return $this->is_included;
    }

    /**
     * Verifica se o módulo está disponível para contratação neste tier.
     *
     * @return boolean
     */
    public function isAvailable(): bool
    {
        return $this->is_available;
    }
}
