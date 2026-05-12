<?php

namespace App\Models\Tracking;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasUuids;

    protected $connection = 'conn_rhdpeople_landingpage';

    protected $fillable = [
        'session_id',
        'name',
        'email',
        'phone',
        'status',
        'last_field_filled',
        'converted_at',
    ];

    protected $casts = [
        'converted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
