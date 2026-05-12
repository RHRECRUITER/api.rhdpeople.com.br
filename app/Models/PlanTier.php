<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlanTier extends Model
{
    use HasUuids;

    protected $table = 'plan_tiers';

    protected $fillable = [
        'name',
        'description',
        'base_price',
        'annual_discount_pct',
        'max_employees',
        'is_custom_pricing',
        'is_active',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'annual_discount_pct' => 'decimal:2',
        'max_employees' => 'integer',
        'is_custom_pricing' => 'boolean',
        'is_active' => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────────

    public function employeeRanges(): HasMany
    {
        return $this->hasMany(EmployeeRangeMultiplier::class, 'plan_tier_id');
    }

    public function tierModules(): HasMany
    {
        return $this->hasMany(PlanTierModule::class, 'plan_tier_id');
    }

    public function tierAddOns(): HasMany
    {
        return $this->hasMany(PlanTierAddOn::class, 'plan_tier_id');
    }

    public function plans(): HasMany
    {
        return $this->hasMany(Plan::class, 'plan_tier_id');
    }

    // ─── Helpers ──────────────────────────────────────────────────

    /**
     * Retorna o preço com desconto anual aplicado (valor mensal equivalente).
     */
    public function annualMonthlyPrice(): float
    {
        return round(
            $this->base_price * (1 - $this->annual_discount_pct / 100),
            2
        );
    }

    /**
     * Verifica se o plano aceita um número de colaboradores.
     */
    public function acceptsEmployeeCount(int $count): bool
    {
        if ($this->max_employees === null) {
            return true;
        }

        return $count <= $this->max_employees;
    }
}
