<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanModule extends Model
{
    use HasUuids;

    protected $table = 'plan_modules';

    protected $fillable = [
        'plan_id',
        'module_id',
        'is_included',
        'price_charged',
    ];

    protected $casts = [
        'is_included' => 'boolean',
        'price_charged' => 'decimal:2',
    ];

    // ─── Relationships ────────────────────────────────────────────

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class, 'module_id');
    }

    // ─── Helpers ──────────────────────────────────────────────────

    /**
     * Verifica se o módulo foi incluso sem custo neste plano.
     */
    public function isIncluded(): bool
    {
        return $this->is_included;
    }

    /**
     * Verifica se o módulo foi contratado como extra pago.
     */
    public function isPaid(): bool
    {
        return ! $this->is_included && $this->price_charged > 0;
    }
}
