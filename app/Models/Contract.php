<?php

namespace App\Models;

use App\Enums\SubscriptionInterval;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Contract extends Model
{
    use HasUuids, BelongsToTenant;

    protected $table = 'contracts';

    protected $fillable = [
        'tenant_id',
        'gateway_subscription_id',
        'gateway_customer_id',
        'start_date',
        'expiration_date',
        'billing_cycle',
        'auto_renew',
    ];

    protected $casts = [
        'start_date'      => 'date',
        'expiration_date' => 'date',
        'billing_cycle'   => SubscriptionInterval::class,
        'auto_renew'      => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────────

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'tenant_id');
    }

    public function plan(): HasOne
    {
        return $this->hasOne(Plan::class, 'contract_id');
    }

    // ─── Helpers ──────────────────────────────────────────────────

    /**
     * Verifica se o contrato está vencido.
     *
     * @return boolean
     */
    public function isExpired(): bool
    {
        return $this->expiration_date->isPast();
    }

    /**
     * Verifica se o contrato vence nos próximos N dias.
     *
     * @param integer $days
     * @return boolean
     */
    public function isDueSoon(int $days = 30): bool
    {
        return $this->expiration_date->isBetween(now(), now()->addDays($days));
    }

    /**
     * Verifica se o contrato está ativo (não vencido).
     *
     * @return boolean
     */
    public function isActive(): bool
    {
        return ! $this->isExpired();
    }
}
