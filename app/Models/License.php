<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Enums\TypeLicense;
use App\Models\Plan;
use App\Models\User;

class License extends Model
{
    //Uuids
    use HasUuids, HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'type_license',
        'plan_id',
        'user_id',
    ];
    protected $casts = [
        'type_license' => TypeLicense::class,
    ];

    public function plan(): BelongsTo{
        return $this->belongsTo(Plan::class);
    }

    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }


};


