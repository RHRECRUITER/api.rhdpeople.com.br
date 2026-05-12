<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeRangeMultiplier extends Model
{
    use HasUuids;

    protected $table = 'employee_range_multipliers';

    protected $fillable = [
        'plan_tier_id',
        'range_label',
        'min_employees',
        'max_employees',
        'price_multiplier',
    ];

    protected $casts = [
        'min_employees' => 'integer',
        'max_employees' => 'integer',
        'price_multiplier' => 'decimal:2',
    ];

    // ─── Relationships ────────────────────────────────────────────

    public function planTier(): BelongsTo
    {
        return $this->belongsTo(PlanTier::class, 'plan_tier_id');
    }

    public function plans(): HasMany
    {
        return $this->hasMany(Plan::class, 'employee_range_id');
    }

    // ─── Helpers ──────────────────────────────────────────────────

    /**
     * Verifica se um número de colaboradores se encaixa nesta faixa.
     */
    public function containsCount(int $count): bool
    {
        if ($this->max_employees === null) {
            return $count >= $this->min_employees;
        }

        return $count >= $this->min_employees && $count <= $this->max_employees;
    }

    /**
     * Calcula o preço final aplicando o multiplicador ao preço base do tier.
     */
    public function applyTo(float $basePrice): float
    {
        return round($basePrice * $this->price_multiplier, 2);
    }
}
