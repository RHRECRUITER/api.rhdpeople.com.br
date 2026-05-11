<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanAddOn extends Model
{
    use HasUuids;

    protected $table = 'plan_add_ons';

    protected $fillable = [
        'plan_id',
        'add_on_id',
        'price_charged',
    ];

    protected $casts = [
        'price_charged' => 'decimal:2',
    ];

    // ─── Relationships ────────────────────────────────────────────

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function addOn(): BelongsTo
    {
        return $this->belongsTo(AddOn::class, 'add_on_id');
    }

    // ─── Helpers ──────────────────────────────────────────────────

    /**
     * Retorna o preço formatado para exibição.
     *
     * @return string
     */
    public function displayPrice(): string
    {
        return '+R$ ' . number_format($this->price_charged, 2, ',', '.');
    }
}
