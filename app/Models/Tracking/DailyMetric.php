<?php

namespace App\Models\Tracking;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class DailyMetric extends Model
{
    use HasUuids;
    protected $connection = 'conn_rhdpeople_landingpage';

    protected $fillable = [
        'date',
        'page_path',
        'total_views',
        'total_sessions',
        'total_conversions',
        'bounce_rate'
    ];

    protected $casts = [
        'date' => 'date',
        'bounce_rate' => 'float',
        'total_views' => 'integer',
        'total_sessions' => 'integer',
        'total_conversions' => 'integer',
    ];
}