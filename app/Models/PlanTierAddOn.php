<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanTierAddOn extends Model
{
    use HasUuids;

    protected $table = 'plan_tier_add_ons';

    protected $fillable = [
        'plan_tier_id',
        'add_on_id',
        'is_available',
    ];

    protected $casts = [
        'is_available' => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────────

    public function planTier(): BelongsTo
    {
        return $this->belongsTo(PlanTier::class, 'plan_tier_id');
    }

    public function addOn(): BelongsTo
    {
        return $this->belongsTo(AddOn::class, 'add_on_id');
    }

    // ─── Helpers ──────────────────────────────────────────────────

    /**
     * Verifica se o add-on está disponível para contratação neste tier.
     */
    public function isAvailable(): bool
    {
        return $this->is_available;
    }
}
