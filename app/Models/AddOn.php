<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AddOn extends Model
{
    use HasUuids;

    protected $table = 'add_ons';

    protected $fillable = [
        'key',
        'label',
        'price',
        'description',
        'is_active',
    ];

    protected $casts = [
        'price'     => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────────

    public function tierAddOns(): HasMany
    {
        return $this->hasMany(PlanTierAddOn::class, 'add_on_id');
    }

    public function planAddOns(): HasMany
    {
        return $this->hasMany(PlanAddOn::class, 'add_on_id');
    }

    // ─── Helpers ──────────────────────────────────────────────────

    /**
     * Retorna o preço formatado para exibição no front.
     *
     * @return string
     */
    public function displayPrice(): string
    {
        return '+R$ ' . number_format($this->price, 2, ',', '.');
    }
}
