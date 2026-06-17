<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasUuids;

    protected $connection = 'conn_rhdpeople_geral';

    protected $table = 'audit_logs';

    public $timestamps = false;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'tenant_id',
        'actor_id',
        'action',
        'timestamp',
        'payload',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'payload' => 'array',
    ];
}
