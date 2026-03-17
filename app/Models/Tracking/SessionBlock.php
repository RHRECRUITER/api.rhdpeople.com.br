<?php

namespace App\Models\Tracking;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SessionBlock extends Model
{
    use HasUuids, BelongsToTenant;
    protected $connection = 'conn_rhdpeople_landingpage';

    protected $fillable = [
        'session_id',
        'tenant_id', 
        'user_id',   
        'started_at',
        'ended_at',
        'duration_seconds',
        'landing_page',
        'exit_page',
        'is_converted'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'is_converted' => 'boolean',
        'duration_seconds' => 'integer'
    ];

    /**
     * RELACIONAMENTO
     * 
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'session_id', 'session_id');
    }
}