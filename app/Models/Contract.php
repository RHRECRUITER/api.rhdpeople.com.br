<?php

namespace App\Models;

use App\Models\Plan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Contract extends Model
{
    //Uuids
    use HasUuids, HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'expiration_date'
    ];
    protected $casts = [
        'expiration_date' => 'date',
    ];

    public function Plan(): belongsTo{
        return $this->belongsTo(Plan::class);
    }


}

