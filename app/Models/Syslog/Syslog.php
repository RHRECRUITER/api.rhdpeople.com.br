<?php

namespace App\Models\Syslog;

use App\Models\Company;
use App\Models\User;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Parental\HasChildren;

class Syslog extends Model
{
    use HasChildren, BelongsToTenant;

    protected $connection = 'conn_rhdpeople_geral';

    public $timestamps = false;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'correlation_id',
        'action',
        'severity',
        'auditable_type',
        'auditable_id',
        'payload',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    protected array $childTypes = [
        'authentication' => AuthenticationLog::class,
        'contract'       => ContractLog::class,
        'license'        => LicenseLog::class,
        'tenant_admin'   => TenantAdminLog::class,
        'webhook'        => WebhookLog::class,
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $model): void {
            if (! $model->getAttribute('created_at')) {
                $model->setAttribute('created_at', now());
            }
        });

        static::updating(function (): void {
            throw new \LogicException('sys_logs is append-only — updates are not allowed.');
        });

        static::deleting(function (): void {
            throw new \LogicException('sys_logs is append-only — deletes are not allowed.');
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'tenant_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }
}
