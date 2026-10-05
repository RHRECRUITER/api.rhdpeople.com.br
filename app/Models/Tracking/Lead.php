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
        'employee_range',
        'challenge',
        'company_name',
        'last_field_filled',
        'converted_at',
    ];

    protected $casts = [
        'challenge' => 'array',
        'converted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
