<?php

namespace App\Models;

use App\Enums\SubscriptionInterval;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use BelongsToTenant, HasUuids;

    protected $table = 'plans';

    protected $fillable = [
        'tenant_id',
        'contract_id',
        'plan_tier_id',
        'employee_range_id',
        'billing_cycle',
        'price_base',
        'price_with_multiplier',
        'price_modules',
        'price_add_ons',
        'price_subtotal',
        'price_discount',
        'price_total',
        'status',
    ];

    protected $casts = [
        'billing_cycle' => SubscriptionInterval::class,
        'price_base' => 'decimal:2',
        'price_with_multiplier' => 'decimal:2',
        'price_modules' => 'decimal:2',
        'price_add_ons' => 'decimal:2',
        'price_subtotal' => 'decimal:2',
        'price_discount' => 'decimal:2',
        'price_total' => 'decimal:2',
    ];

    // ─── Relationships ────────────────────────────────────────────

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'tenant_id');
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class, 'contract_id');
    }

    public function planTier(): BelongsTo
    {
        return $this->belongsTo(PlanTier::class, 'plan_tier_id');
    }

    public function employeeRange(): BelongsTo
    {
        return $this->belongsTo(EmployeeRangeMultiplier::class, 'employee_range_id');
    }

    public function planModules(): HasMany
    {
        return $this->hasMany(PlanModule::class, 'plan_id');
    }

    public function planAddOns(): HasMany
    {
        return $this->hasMany(PlanAddOn::class, 'plan_id');
    }

    public function licenses(): HasMany
    {
        return $this->hasMany(License::class, 'plan_id');
    }

    // ─── Helpers ──────────────────────────────────────────────────

    /**
     * Verifica se o plano está ativo.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Verifica se o plano está em trial.
     */
    public function isTrial(): bool
    {
        return $this->status === 'trial';
    }

    /**
     * Verifica se o plano está suspenso.
     */
    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    /**
     * Verifica se o plano está cancelado.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Retorna o total de licenças ativas no plano.
     */
    public function activeLicensesCount(): int
    {
        return $this->licenses()->where('is_active', true)->count();
    }

    /**
     * Verifica se o plano ainda tem vagas disponíveis.
     */
    public function hasAvailableSlots(): bool
    {
        $max = $this->employeeRange->max_employees;

        // Quando não quisermos definir um limite de empregados para um
        // cliente, devemos colocar null no max employees
        if ($max === null) {
            return true;
        }

        return $this->activeLicensesCount() < $max;
    }

    /**
     * Retorna quantas vagas ainda estão disponíveis.
     */
    public function availableSlots(): ?int
    {
        $max = $this->employeeRange->max_employees;

        // Precisa tratar que quando for null o maximo
        // significa que é para passar direto
        if ($max === null) {
            return null;
        }

        return max(0, $max - $this->activeLicensesCount());
    }
}
